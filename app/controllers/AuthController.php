<?php
require_once '../app/models/Usuario.php';

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

        $codigo = trim($_POST['codigo'] ?? '');

        if ($codigo === '') {
            $error = "Debe ingresar el código de verificación.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        // Aquí después validaremos el TOTP real
        // Por ahora dejamos una validación temporal para probar flujo
        if ($codigo !== '123456') {
            $error = "Código de verificación inválido.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        // Si el código es válido, ahora sí se crea la sesión final
        session_regenerate_id(true);

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
}