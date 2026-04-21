<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-dumbbell"></i> Planes de Membresía</h3>
                <a href="/planes/crear" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Nuevo Plan</a>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered align-middle table-data">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Duración (Días)</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($planes as $plan): ?>
                        <tr>
                            <td><strong><?= $plan['nombre'] ?></strong></td>
                            
                            <td><?= $config['moneda'] ?> <?= number_format($plan['precio'], 2) ?></td>
                            
                            <td><?= $plan['duracion_dias'] ?> días</td>
                            <td><?= $plan['descripcion'] ?></td>
                            <td>
                                <?php if($plan['estado'] == 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/planes/editar/<?= $plan['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <?php if($plan['estado'] == 'activo'): ?>
                                    <a href="/planes/cambiarEstado/<?= $plan['id'] ?>/inactivo" 
                                       class="btn btn-danger btn-sm btn-confirm"
                                       data-title="¿Desactivar este plan?">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="/planes/cambiarEstado/<?= $plan['id'] ?>/activo" 
                                       class="btn btn-success btn-sm btn-confirm"
                                       data-title="¿Reactivar este plan?">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>