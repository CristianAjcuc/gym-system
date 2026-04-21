<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Apertura de Caja - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        
        <?php if(isset($_GET['msg']) && $_GET['msg']=='cerrado'): ?>
            <div class="alert alert-success text-center mb-4 shadow-sm">
                <h4><i class="fas fa-check-circle"></i> Turno cerrado correctamente</h4>
                <p class="m-0">La caja está lista para una nueva apertura.</p>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 border-top border-primary border-5">
                    <div class="card-header bg-white text-center py-4">
                        <h2 class="text-primary fw-bold"><i class="fas fa-cash-register"></i> Apertura de Caja</h2>
                        <p class="text-muted m-0">Ingrese el dinero base para iniciar operaciones</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="/caja/abrir" method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">Monto Inicial (Sencillo)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-primary text-white fw-bold"><?= $config['moneda'] ?></span>
                                    <input type="number" step="0.01" name="monto_inicial" class="form-control fw-bold text-primary" placeholder="0.00" required autofocus>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg shadow">INICIAR TURNO</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="m-0"><i class="fas fa-history"></i> Historial de Cierres Recientes</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover table-data">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha Cierre</th>
                            <th>Cajero</th>
                            <th>Inicial</th>
                            <th>Ventas</th>
                            <th>Gastos</th>
                            <th>Esperado</th>
                            <th>Real</th>
                            <th>Cuadre</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($historial as $h): ?>
                            <?php if($h['estado'] == 'cerrada'): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($h['fecha_cierre'])) ?></td>
                                <td><?= $h['cajero'] ?></td>
                                <td><?= $config['moneda'] . number_format($h['monto_inicial'], 2) ?></td>
                                <td class="text-success fw-bold">+<?= number_format($h['total_ventas'], 2) ?></td>
                                <td class="text-danger fw-bold">-<?= number_format($h['total_gastos'], 2) ?></td>
                                
                                <?php $sistema = $h['monto_inicial'] + $h['total_ventas'] - $h['total_gastos']; ?>
                                <td class="fw-bold bg-light"><?= $config['moneda'] . number_format($sistema, 2) ?></td>
                                
                                <td><?= $config['moneda'] . number_format($h['monto_final'], 2) ?></td>
                                
                                <td>
                                    <?php if($h['diferencia'] == 0): ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> OK</span>
                                    <?php elseif($h['diferencia'] < 0): ?>
                                        <span class="badge bg-danger">Falta <?= $h['diferencia'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Sobra <?= $h['diferencia'] ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>