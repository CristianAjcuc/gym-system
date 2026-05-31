<?php
require_once '../app/models/Usuario.php';
require_once '../app/lib/TotpHelper.php';
require_once '../app/lib/CryptoHelper.php';

class AuthController {

    public function index() {
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
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';

            $usuarioModel = new Usuario();

            $intentoLogin = $usuarioModel->obtenerIntentoLogin($email, $ip);

            if ($intentoLogin && !empty($intentoLogin['blocked_until'])) {
                $blockedUntil = strtotime($intentoLogin['blocked_until']);

                if ($blockedUntil > time()) {
                    $remainingSeconds = $blockedUntil - time();

                    error_log("LOGIN_BLOQUEADO IP={$ip} EMAIL={$email} REMAINING_SECONDS={$remainingSeconds}");

                    $usuarioModel->registrarAuditoriaAuth(
                        null,
                        $email,
                        $ip,
                        'LOGIN_BLOQUEADO',
                        'BLOQUEADO',
                        "Bloqueo activo. Restan {$remainingSeconds} segundos.",
                        $userAgent
                    );

                    $error = "Demasiados intentos fallidos. Espere {$remainingSeconds} segundos.";
                    require_once '../app/views/auth/login.php';
                    return;
                }
            }

            $usuario = $usuarioModel->login($email, $password);

            if ($usuario === 'inactivo') {
                $error = "Cuenta inhabilitada. Contacte al administrador.";
                require_once '../app/views/auth/login.php';
                return;

            } elseif ($usuario) {
                $usuarioModel->limpiarIntentosLogin($email, $ip);

                $usuarioModel->registrarAuditoriaAuth(
                    $usuario['id'],
                    $email,
                    $ip,
                    'LOGIN_EXITOSO',
                    'EXITOSO',
                    'Credenciales correctas.',
                    $userAgent
                );

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

                session_regenerate_id(true);
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['user_rol'] = $usuario['rol'];

                if (
                    empty($usuario['two_factor_enabled']) ||
                    (int)$usuario['two_factor_enabled'] !== 1 ||
                    empty($usuario['two_factor_confirmed_at'])
                ) {
                    $_SESSION['must_configure_2fa'] = true;
                    header('Location: /auth/configurar2fa');
                    exit;
                }

                header('Location: /home/index');
                exit;

            } else {
                $attempts = $intentoLogin ? ((int)$intentoLogin['attempts'] + 1) : 1;

                $loginDelays = [1 => 0, 2 => 0, 3 => 0, 4 => 10, 5 => 30, 6 => 60];
                $delay = $loginDelays[$attempts] ?? 60;

                $blockedUntil = null;

                if ($delay > 0) {
                    $blockedUntil = date('Y-m-d H:i:s', time() + $delay);
                }

                $usuarioModel->registrarIntentoFallidoLogin($email, $ip, $attempts, $blockedUntil);

                error_log("LOGIN_FALLIDO IP={$ip} EMAIL={$email} ATTEMPT={$attempts} DELAY={$delay} UA={$userAgent}");

                $usuarioModel->registrarAuditoriaAuth(
                    null,
                    $email,
                    $ip,
                    'LOGIN_FALLIDO',
                    'FALLIDO',
                    "Intento {$attempts}. Delay {$delay} segundos.",
                    $userAgent
                );

                if ($delay > 0) {
                    $error = "Correo o contraseña incorrectos. Espere {$delay} segundos antes de intentar nuevamente.";
                } else {
                    $error = "Correo o contraseña incorrectos.";
                }

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
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
        $remainingBlockSeconds = 0;

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($_SESSION['pending_2fa_user_id']);

        if (!$usuario) {
            $error = "No se encontró información del usuario.";
            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        if (!empty($_SESSION['pending_2fa_block_until'])) {
            $remainingBlockSeconds = max(0, $_SESSION['pending_2fa_block_until'] - $now);

            if ($remainingBlockSeconds > 0) {
                error_log("2FA_BLOQUEADO IP={$ip} USER_ID={$usuario['id']} REMAINING_SECONDS={$remainingBlockSeconds}");

                $usuarioModel->registrarAuditoriaAuth(
                    $usuario['id'],
                    $usuario['email'] ?? null,
                    $ip,
                    '2FA_BLOQUEADO',
                    'BLOQUEADO',
                    "Restan {$remainingBlockSeconds} segundos.",
                    $userAgent
                );

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

        if (empty($usuario['two_factor_secret'])) {
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

            $delays = [1 => 0, 2 => 5, 3 => 5, 4 => 60];
            $delay = $delays[$attempts] ?? 60;

            error_log("2FA_FALLIDO IP={$ip} USER_ID={$usuario['id']} ATTEMPT={$attempts} DELAY={$delay}");

            $usuarioModel->registrarAuditoriaAuth(
                $usuario['id'],
                $usuario['email'] ?? null,
                $ip,
                '2FA_FALLIDO',
                'FALLIDO',
                "Intento {$attempts}. Delay {$delay} segundos.",
                $userAgent
            );

            if ($delay > 0) {
                $_SESSION['pending_2fa_block_until'] = $now + $delay;
                $remainingBlockSeconds = $delay;

                $error = "Código de verificación inválido. Intento {$attempts}. Intente de nuevo en {$delay} segundos.";
            } else {
                unset($_SESSION['pending_2fa_block_until']);
                $remainingBlockSeconds = 0;

                $error = "Código de verificación inválido. Intento {$attempts}. Verifique el código e intente nuevamente.";
            }

            require_once '../app/views/auth/verificar2fa.php';
            return;
        }

        $usuarioModel->registrarAuditoriaAuth(
            $usuario['id'],
            $usuario['email'] ?? null,
            $ip,
            '2FA_EXITOSO',
            'EXITOSO',
            'Código 2FA validado correctamente.',
            $userAgent
        );

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
        if (isset($_SESSION['user_id'])) {
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']);

            $usuarioModel->registrarAuditoriaAuth(
                $_SESSION['user_id'],
                $usuario['email'] ?? null,
                $_SERVER['REMOTE_ADDR'] ?? 'N/A',
                'LOGOUT',
                'EXITOSO',
                'Cierre de sesión del usuario.',
                $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
            );
        }

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

        if (!empty($usuario['two_factor_secret'])) {
            $secretPlano = CryptoHelper::decrypt($usuario['two_factor_secret']);

            if (!empty($secretPlano)) {
                $secret = $secretPlano;
                $otpauth = TotpHelper::getOtpAuthUrl('GymSystem', $usuario['email'], $secretPlano);
                require_once '../app/views/auth/configurar2fa.php';
                return;
            }
        }

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
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorId($_SESSION['user_id']);

        if (!$usuario || empty($usuario['two_factor_secret'])) {
            $error = "No existe un secreto configurado para activar el doble factor.";
            require_once '../app/views/auth/configurar2fa.php';
            return;
        }

        $secretPlano = CryptoHelper::decrypt($usuario['two_factor_secret']);

        if (empty($secretPlano)) {
            $error = "No se pudo leer la configuración de doble factor.";
            require_once '../app/views/auth/configurar2fa.php';
            return;
        }

        $otpauth = TotpHelper::getOtpAuthUrl('GymSystem', $usuario['email'], $secretPlano);

        if (!TotpHelper::verifyCode($secretPlano, $codigo)) {
            $error = "El código ingresado no es válido.";
            require_once '../app/views/auth/configurar2fa.php';
            return;
        }

        $usuarioModel->activar2FA($usuario['id']);

        $usuarioModel->registrarAuditoriaAuth(
            $usuario['id'],
            $usuario['email'],
            $ip,
            '2FA_ACTIVADO',
            'EXITOSO',
            'Doble factor activado correctamente.',
            $userAgent
        );

        unset($_SESSION['must_configure_2fa']);

        $_SESSION['success_message'] = "Doble factor activado correctamente.";
        header('Location: /home/index');
        exit;
    }
}