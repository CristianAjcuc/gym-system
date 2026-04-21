<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container pb-5">
        
        <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
            
            <h2 class="mb-4 text-secondary"><i class="fas fa-tachometer-alt"></i> Panel de Control</h2>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow border-0 border-start border-primary border-5 h-100">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Socios Activos</h6>
                            <h2 class="text-primary fw-bold"><?= $totalSocios ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 border-start border-success border-5 h-100">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Ingresos Mes</h6>
                            <h2 class="text-success fw-bold"><?= $config['moneda'] ?> <?= number_format($ingresosMes, 2) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 border-start border-danger border-5 h-100">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Gastos Mes</h6>
                            <h2 class="text-danger fw-bold">-<?= $config['moneda'] ?> <?= number_format($gastosMes, 2) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow border-0 border-start border-warning border-5 h-100">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Utilidad Neta</h6>
                            <h2 class="<?= $utilidad >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                <?= $config['moneda'] ?> <?= number_format($utilidad, 2) ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card shadow h-100">
                        <div class="card-header bg-white fw-bold"><i class="fas fa-chart-bar"></i> Tendencia de Ventas</div>
                        <div class="card-body"><canvas id="ventasChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow h-100">
                        <div class="card-header bg-white fw-bold"><i class="fas fa-chart-pie"></i> Planes Populares</div>
                        <div class="card-body d-flex justify-content-center align-items-center" style="height: 250px;">
                            <canvas id="planesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const ctx = document.getElementById('ventasChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels) ?>, 
                        datasets: [{ label: 'Ventas', data: <?= json_encode($data) ?>, backgroundColor: '#36A2EB' }]
                    }
                });
                const ctx2 = document.getElementById('planesChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($planesLabels) ?>,
                        datasets: [{ data: <?= json_encode($planesData) ?>, backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF'] }]
                    },
                    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });
            </script>

        <?php else: ?>
            
            <div class="p-5 mb-4 bg-white rounded-3 shadow text-center">
                <div class="container-fluid py-5">
                    <h1 class="display-5 fw-bold">Hola, <?= $_SESSION['user_name'] ?? 'Usuario' ?></h1>
                    
                    <p class="col-md-8 fs-4 mx-auto">
                        Bienvenido al sistema. Tu rol es: 
                        <strong class="text-uppercase text-primary"><?= ucfirst($_SESSION['user_rol'] ?? 'Invitado') ?></strong>.
                    </p>
                    
                    <p class="text-muted">Acciones rápidas disponibles para tu perfil:</p>
                    
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center mt-4">
                        
                        <a href="/asistencia/index" class="btn btn-warning btn-lg px-4 gap-3 fw-bold shadow-sm">
                            <i class="fas fa-clock"></i> Control Asistencia
                        </a>

                        <?php if($_SESSION['user_rol'] == 'recepcionista'): ?>
                            <a href="/suscripciones/crear" class="btn btn-primary btn-lg px-4 gap-3 shadow-sm">
                                <i class="fas fa-plus"></i> Nueva Venta
                            </a>
                            <a href="/caja/index" class="btn btn-outline-dark btn-lg px-4 shadow-sm">
                                <i class="fas fa-cash-register"></i> Ver Caja
                            </a>

                        <?php elseif($_SESSION['user_rol'] == 'entrenador'): ?>
                            <a href="/socios/index" class="btn btn-success btn-lg px-4 gap-3 shadow-sm">
                                <i class="fas fa-dumbbell"></i> Gestionar Rutinas
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-white fw-bold text-danger">
                        <i class="fas fa-clock"></i> Próximos Vencimientos (7 días)
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Socio</th><th>Plan</th><th>Fin</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php if(empty($vencimientos)): ?>
                                    <tr><td colspan="4" class="text-center p-3 text-muted">No hay vencimientos cercanos.</td></tr>
                                <?php else: ?>
                                    <?php foreach($vencimientos as $v): ?>
                                    <tr>
                                        <td><?= $v['socio'] ?></td>
                                        <td><?= $v['plan'] ?></td>
                                        <td class="text-danger fw-bold"><?= date('d/m/Y', strtotime($v['fecha_fin'])) ?></td>
                                        <td>
                                            <?php if($_SESSION['user_rol'] != 'entrenador'): ?>
                                                <a href="/suscripciones/crear" class="btn btn-sm btn-outline-success">Renovar</a>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Por Vencer</span>
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
        </div>

    </div>
    
    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>