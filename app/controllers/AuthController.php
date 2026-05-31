<?php
require_once '../app/models/Usuario.php';
require_once '../app/lib/TotpHelper.php';
require_once '../app/lib/CryptoHelper.php';

class AuthController {
    
    public function index() {
        // Crear admin si es la primera vez
        $usuarioModel = new Usuario();
        $usuarioModel->crearAdmin();

        if (isset($_SESSION['user_id'])) {
            header('Location: /home/index');
            exit;
        }

        require_once '../app/views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $usuarioModel = new Usuario();
            // El modelo devuelve un array, false, o el string 'inactivo'
            $usuario = $usuarioModel->login($email, $password);

            if ($usuario === 'inactivo') {
                // CASO: Usuario desactivado
                $error = "Cuenta inhabilitada. Contacte al administrador.";
                require_once '../app/views/auth/login.php';
                return;

            } elseif ($usuario) {
                // CASO: Éxito en usuario/contraseña

                // Si el usuario tiene 2FA activo, todavía NO entra al sistema
                if (
                    isset($usuario['two_factor_enabled']) &&
                    (int)$usuario['two_factor_enabled'] === 1 &&
                    !empty($usuario['two_factor_secret'])
                ) {
                    $_SESSION['pending_2fa_user_id'] = $usuario['id'];
                    $_SESSION['pending_2fa_user_name'] = $usuario['nombre'];
                    $_SESSION['pending_2fa_user_rol'] = $usuario['rol'];

                    header('Location: /auth/verificar2faForm');
                    exit;
                }

                // Si no tiene 2FA, entra normal
                session_regenerate_id(true);
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['user_rol'] = $usuario['rol'];
                
                header('Location: /home/index');
                exit;

            } else {
                // CASO: Datos incorrectos
                $error = "Correo o contraseña incorrectos.";
                require_once '../app/views/auth/login.php';
                return;
            }
        }

        header('Location: /auth/index');
        exit;
    }

    public function verificar2faForm() {
        if (!isset($_SESSION['pending_2fa_user_id'])) {
            header('Location: /auth/index');
            exit;
        }

        $remainingBlockSeconds = 0;
        if (!empty($_SESSION['pending_2fa_block_until'])) {
            $remainingBlockSeconds = max(0, $_SESSION['pending_2fa_block_until'] - time());
            if ($remainingBlockSeconds === 0) {
                unset($_SESSION['pending_2fa_block_until']);
            }
        }

        require_once '../app/views/auth/verificar2fa.php';
    }

    public function verificar2fa() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/index');
            exit;
        }

        if (!isset($_SESSION['pending_2fa_user_id'])) {
            header('Location: /auth/index');
            exit;
        }

        $now = time();
        $remainingBlockSeconds = 0;
        if (!empty($_SESSION['pending_2fa_block_until'])) {
            $remainingBlockSeconds = max(0, $_SESSION['pending_2fa_block_until'] - $now);
            if ($remainingBlockSeconds > 0) {
                $error = "Demasiados intentos. Intente de nuevo en {$remainingBlockSeconds} segundos.";
                require_once '../app/views/auth/verificar2fa.php';
                return;
            }

            unset($_SESSION['pending_2fa_block_until']);
        }

        $codigo = trim($_POST['codigo'] ?? '');

        if ($codigo === '') {
            $error = "Debe ingresar el código de verificación.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        // Aquí después validaremos el TOTP real
        // Por ahora dejamos una validación temporal para probar flujo
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($_SESSION['pending_2fa_user_id']);

        if (!$usuario || empty($usuario['two_factor_secret'])) {
            $error = "No se encontró configuración de doble factor para este usuario.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        $secretPlano = CryptoHelper::decrypt($usuario['two_factor_secret']);

        if (empty($secretPlano)) {
            $error = "No se pudo leer la configuración de doble factor.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        if (!TotpHelper::verifyCode($secretPlano, $codigo)) {
            $attempts = (!empty($_SESSION['pending_2fa_failed_attempts']) ? $_SESSION['pending_2fa_failed_attempts'] : 0) + 1;
            $_SESSION['pending_2fa_failed_attempts'] = $attempts;

            $delays = [1 => 5, 2 => 30, 3 => 300];
            $delay = $delays[$attempts] ?? 300;
            $_SESSION['pending_2fa_block_until'] = $now + $delay;
            $remainingBlockSeconds = $delay;

            $error = "Código de verificación inválido. Intento {$attempts}. Intente de nuevo en {$delay} segundos.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        // Si el código es válido, ahora sí se crea la sesión final
        unset($_SESSION['pending_2fa_block_until'], $_SESSION['pending_2fa_failed_attempts']);

        $_SESSION['user_id'] = $_SESSION['pending_2fa_user_id'];
        $_SESSION['user_name'] = $_SESSION['pending_2fa_user_name'];
        $_SESSION['user_rol'] = $_SESSION['pending_2fa_user_rol'];

        unset($_SESSION['pending_2fa_user_id']);
        unset($_SESSION['pending_2fa_user_name']);
        unset($_SESSION['pending_2fa_user_rol']);

        header('Location: /home/index');
        exit;
    }

    public function logout() {

        session_destroy();
        header('Location: /auth/index');
        exit;
    }

    public function configurar2fa() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /auth/index');
        exit;
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']);

    if (!$usuario) {
        header('Location: /home/index');
        exit;
    }

    // Si ya existe un secreto guardado, NO generar otro ni resetear 2FA
    if (!empty($usuario['two_factor_secret'])) {
        $secretPlano = CryptoHelper::decrypt($usuario['two_factor_secret']);

        if (!empty($secretPlano)) {
            $secret = $secretPlano;
            $otpauth = TotpHelper::getOtpAuthUrl('GymSystem', $usuario['email'], $secretPlano);
            require_once '../app/views/auth/configurar2fa.php';
            return;
        }
    }

    // Solo si no existe secreto, generar uno nuevo
    $secret = TotpHelper::generateSecret();
    $usuarioModel->guardarSecret2FA($usuario['id'], $secret);

    $otpauth = TotpHelper::getOtpAuthUrl('GymSystem', $usuario['email'], $secret);

    require_once '../app/views/auth/configurar2fa.php';
}
public function activar2fa() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
        header('Location: /auth/index');
        exit;
    }

    $codigo = trim($_POST['codigo'] ?? '');

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']);

    if (!$usuario || empty($usuario['two_factor_secret'])) {
        $error = "No existe un secreto configurado para activar el doble factor.";
        require_once '../app/views/auth/configurar2fa.php';
        return;
    }

    // Descifrar el secreto guardado en BD
    $secretPlano = CryptoHelper::decrypt($usuario['two_factor_secret']);

    if (empty($secretPlano)) {
        $error = "No se pudo leer la configuración de doble factor.";
        require_once '../app/views/auth/configurar2fa.php';
        return;
    }

    // Reconstruir el QR SIEMPRE antes de volver a la vista
    $otpauth = TotpHelper::getOtpAuthUrl('GymSystem', $usuario['email'], $secretPlano);

    if (!TotpHelper::verifyCode($secretPlano, $codigo)) {
        $error = "El código ingresado no es válido.";
        require_once '../app/views/auth/configurar2fa.php';
        return;
    }

    $usuarioModel->activar2FA($usuario['id']);
    $_SESSION['success_message'] = "Doble factor activado correctamente.";
    header('Location: /home/index');
    exit;
}

}