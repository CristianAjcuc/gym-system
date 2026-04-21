<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-money-bill-wave"></i> Registro de Gastos</h3>
                <a href="/gastos/crear" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Registrar Gasto</a>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered table-data">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gastos as $g): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                            <td><?= $g['descripcion'] ?></td>
                            
                            <td class="text-danger fw-bold">
                                -<?= $config['moneda'] ?> <?= number_format($g['monto'], 2) ?>
                            </td>
                            
                            <td>
                                <a href="/gastos/eliminar/<?= $g['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger btn-confirm"
                                   data-title="¿Eliminar este gasto?">
                                    <i class="fas fa-trash"></i>
                                </a>
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