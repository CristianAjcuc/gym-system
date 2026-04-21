<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Suscripción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    
    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h4>Nueva Suscripción</h4>
                    </div>
                    <div class="card-body">
                        <form action="/suscripciones/guardar" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Socio</label>
                                <select class="form-select" name="socio_id" required>
                                    <option value="">-- Elige un socio --</option>
                                    <?php foreach($socios as $socio): ?>
                                        <?php if($socio['estado'] == 'activo'): ?>
                                            <option value="<?= $socio['id'] ?>">
                                                <?= $socio['nombre'] ?> (DNI: <?= $socio['dni'] ?>)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Seleccionar Plan</label>
                                <select class="form-select" name="plan_id" required>
                                    <option value="">-- Elige un plan --</option>
                                    <?php foreach($planes as $plan): ?>
                                        <?php if($plan['estado'] == 'activo'): ?>
                                            <option value="<?= $plan['id'] ?>">
                                                <?= $plan['nombre'] ?> - $<?= $plan['precio'] ?> (<?= $plan['duracion_dias'] ?> días)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle"></i> La fecha de fin se calculará automáticamente según el plan elegido.</small>
                            </div>

                            <button type="submit" class="btn btn-info text-white w-100">Registrar Venta</button>
                            <a href="/suscripciones/index" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>