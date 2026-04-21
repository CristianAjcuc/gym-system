<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Progreso del Socio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-4 mb-5">
        
        <div class="card shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <?php if(!empty($socio['foto'])): ?>
                        <img src="/img/socios/<?= $socio['foto'] ?>" class="rounded-circle" width="80" height="80" style="object-fit:cover;">
                    <?php else: ?>
                        <i class="fas fa-user-circle fa-4x text-secondary"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h2 class="mb-0"><?= $socio['nombre'] ?></h2>
                    <p class="text-muted m-0"><i class="fas fa-id-card"></i> DNI: <?= $socio['dni'] ?> | <i class="fas fa-phone"></i> <?= $socio['telefono'] ?></p>
                </div>
                <div class="ms-auto">
                    <a href="/socios/index" class="btn btn-outline-secondary">Volver a Lista</a>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="medidas-tab" data-bs-toggle="tab" data-bs-target="#medidas" type="button">
                    <i class="fas fa-weight"></i> Medidas y Gráficos
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="rutina-tab" data-bs-toggle="tab" data-bs-target="#rutina" type="button">
                    <i class="fas fa-dumbbell"></i> Rutina de Entrenamiento
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="medidas">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow mb-3">
                            <div class="card-header bg-primary text-white">Nueva Medida</div>
                            <div class="card-body">
                                <form action="/progreso/guardar_medida" method="POST">
                                    <input type="hidden" name="socio_id" value="<?= $socio['id'] ?>">
                                    
                                    <div class="mb-2">
                                        <label>Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <label>Peso (Kg)</label>
                                            <input type="number" step="0.01" name="peso" class="form-control" required>
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label>% Grasa</label>
                                            <input type="number" step="0.01" name="grasa" class="form-control">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label>Cintura (cm)</label>
                                            <input type="number" step="0.01" name="cintura" class="form-control">
                                        </div>
                                        <div class="col-6 mb-2">
                                            <label>Brazo (cm)</label>
                                            <input type="number" step="0.01" name="brazo" class="form-control">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100 mt-3">Registrar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <canvas id="pesoChart" style="max-height: 250px;"></canvas>
                            </div>
                        </div>

                        <div class="card shadow">
                            <div class="card-header">Historial</div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Peso</th>
                                            <th>Grasa</th>
                                            <th>Cintura</th>
                                            <th>Brazo</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($medidas as $m): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                                            <td><?= $m['peso'] ?> kg</td>
                                            <td><?= $m['grasa'] ?> %</td>
                                            <td><?= $m['cintura'] ?> cm</td>
                                            <td><?= $m['brazo'] ?> cm</td>
                                            <td>
                                                <a href="/progreso/eliminar_medida/<?= $m['id'] ?>/<?= $socio['id'] ?>" class="text-danger" onclick="return confirm('¿Borrar?');"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="rutina">
                <form action="/progreso/guardar_rutina" method="POST">
                    <input type="hidden" name="socio_id" value="<?= $socio['id'] ?>">
                    
                    <div class="row">
                        <?php 
                            $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                            $campos = ['dia1', 'dia2', 'dia3', 'dia4', 'dia5', 'dia6'];
                        ?>
                        
                        <?php for($i=0; $i<6; $i++): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white text-center fw-bold">
                                    <?= $dias[$i] ?>
                                </div>
                                <div class="card-body p-2">
                                    <textarea name="<?= $campos[$i] ?>" class="form-control border-0" rows="5" placeholder="Ej: 4x12 Press Banca..."><?= $rutina[$campos[$i]] ?? '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                        
                        <div class="col-12 mb-3">
                            <label class="fw-bold">Observaciones Generales:</label>
                            <input type="text" name="observaciones" class="form-control" value="<?= $rutina['observaciones'] ?? '' ?>" placeholder="Ej: Enfocarse en la técnica, beber mucha agua...">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg"><i class="fas fa-save"></i> GUARDAR RUTINA SEMANAL</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const ctx = document.getElementById('pesoChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Evolución de Peso (Kg)',
                    data: <?= json_encode($dataPeso) ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: { responsive: true }
        });

        // Script pequeño para mantener la pestaña activa si recargas (opcional)
        if(window.location.href.indexOf("tab=rutina") > -1) {
            var tabTrigger = new bootstrap.Tab(document.getElementById('rutina-tab'));
            tabTrigger.show();
        }
    </script>

    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>