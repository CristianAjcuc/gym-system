<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA</title>
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
            max-width: 430px;
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
    </style>
</head>
<body>
    <div class="card card-2fa">
        <div class="card-header-custom">
            <h2>Doble factor</h2>
            <p class="mb-0">Ingrese el código de autenticación</p>
        </div>

        <div class="card-body-custom">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/verificar2fa">
                <div class="mb-3">
                    <label for="codigo" class="form-label">Código de 6 dígitos</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="codigo" 
                        name="codigo" 
                        maxlength="6" 
                        required
                        autocomplete="one-time-code"
                        pattern="\d{6}"
                        placeholder="123456">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Verificar
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="/auth/logout">Cancelar</a>
            </div>
        </div>
    </div>
</body>
</html>