<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripciones - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-file-invoice-dollar"></i> Suscripciones Activas</h3>
                
                <div>
                    <a href="/suscripciones/exportarExcel" class="btn btn-success btn-sm me-2">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                    <a href="/suscripciones/crear" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-hover table-bordered align-middle table-data">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Socio</th>
                            <th>Plan (Precio)</th>
                            <th>Inicio</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($suscripciones)): ?>
                             <?php else: ?>
                            <?php foreach ($suscripciones as $sub): ?>
                            <?php 
                                $hoy = date('Y-m-d');
                                $vencida = ($hoy > $sub['fecha_fin'] || $sub['estado'] == 'vencida');
                            ?>
                            <tr class="<?= $vencida ? 'table-danger' : '' ?>">
                                <td><?= $sub['id'] ?></td>
                                <td><?= $sub['nombre_socio'] ?></td>
                                
                                <td>
                                    <?= $sub['nombre_plan'] ?> 
                                    (<?= $config['moneda'] ?><?= $sub['precio'] ?>)
                                </td>
                                
                                <td><?= date('d/m/Y', strtotime($sub['fecha_inicio'])) ?></td>
                                <td><strong><?= date('d/m/Y', strtotime($sub['fecha_fin'])) ?></strong></td>
                                <td>
                                    <?php if($vencida): ?>
                                        <span class="badge bg-danger">Vencida</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/comprobante/generar/<?= $sub['id'] ?>" 
                                       target="_blank" 
                                       class="btn btn-primary btn-sm" title="Imprimir PDF">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <?php if(!$vencida): ?>
                                    <a href="/suscripciones/cancelar/<?= $sub['id'] ?>" 
                                       class="btn btn-danger btn-sm btn-confirm"
                                       data-title="¿Cancelar suscripción de <?= $sub['nombre_socio'] ?>?"
                                       title="Cancelar Suscripción">
                                        <i class="fas fa-times-circle"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>