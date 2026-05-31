<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA</title>
    <link rel="stylesheet" href="/public/assets/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(90deg, #3b82f6, #9333ea);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-2fa {
            width: 100%;
            max-width: 520px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .card-header-custom {
            background: #111827;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }
        .card-body-custom {
            background: #fff;
            padding: 30px;
        }
        .secret-box {
            background: #f3f4f6;
            border: 1px dashed #9ca3af;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            word-break: break-all;
        }
    </style>
</head>
<body>
           
<div class="card card-2fa">
    <div class="card-header-custom">
        <h2>Configurar doble factor</h2>
        <p class="mb-0">Escanee el código QR con su aplicación de autenticación</p>
    </div>

    <div class="card-body-custom">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-info">
            <h5 class="mb-2 text-center">¡Bienvenido a Gym System!</h5>
            <p>Detectamos que esta es la primera vez que accede al sistema.</p>
            <p>Para proteger su cuenta y la información del sistema, es necesario configurar la autenticación de doble factor (2FA).</p>
            <ol class="mb-2">
                <li>Abra su aplicación de autenticación en el teléfono.</li>
                <li>Escanee el código QR mostrado a continuación.</li>
                <li>Ingrese el código de 6 dígitos generado por la aplicación.</li>
            </ol>
            <p class="mb-0">Este proceso se realiza una única vez y permitirá un acceso más seguro a la plataforma.</p>
        </div>

        <div class="text-center mb-3">
            <?php if (!empty($otpauth)): ?>
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?= urlencode($otpauth) ?>"
                    alt="QR 2FA">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Clave secreta manual</label>
            <div class="secret-box">
                <?= htmlspecialchars($secret ?? $secretPlano ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <form method="POST" action="/auth/activar2fa">
            <div class="mb-3">
                <label for="codigo" class="form-label">Código generado por la app</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="codigo" 
                    name="codigo" 
                    maxlength="6" 
                    required
                    pattern="\d{6}"
                    placeholder="123456">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Activar doble factor
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="/auth/logout">Cerrar sesión</a>
        </div>
    </div>
</div>