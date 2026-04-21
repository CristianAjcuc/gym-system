<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socios - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-users"></i> Listado de Socios</h3>
                
                <?php if($_SESSION['user_rol'] != 'entrenador'): ?>
                    <a href="/socios/crear" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Socio
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered align-middle table-data">
                    <thead class="table-dark">
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($socios as $socio): ?>
                        <tr>
                            <td class="text-center" style="width: 80px;">
                                <?php if(!empty($socio['foto'])): ?>
                                    <img src="/img/socios/<?= $socio['foto'] ?>?v=<?= time() ?>" 
                                         alt="Foto" 
                                         class="rounded-circle border border-2 border-primary"
                                         width="50" height="50" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><strong><?= $socio['nombre'] ?></strong></td>
                            <td><?= $socio['dni'] ?></td>
                            <td><?= $socio['email'] ?></td>
                            
                            <td>
                                <?php if($socio['estado'] == 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php elseif($socio['estado'] == 'inactivo'): ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($_SESSION['user_rol'] != 'entrenador'): ?>
                                    <a href="/carnet/generar/<?= $socio['id'] ?>" 
                                       target="_blank" 
                                       class="btn btn-info btn-sm text-white" 
                                       title="Generar Carnet Digital">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                <?php endif; ?>

                                <a href="/progreso/ver/<?= $socio['id'] ?>" 
                                   class="btn btn-secondary btn-sm" 
                                   title="Ver Medidas y Rutina">
                                    <i class="fas fa-chart-line"></i>
                                </a>

                                <?php if($_SESSION['user_rol'] != 'entrenador'): ?>
                                    <a href="/socios/editar/<?= $socio['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if(isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 'admin'): ?>
                                    
                                    <?php if($socio['estado'] != 'inactivo'): ?>
                                        <a href="/socios/cambiarEstado/<?= $socio['id'] ?>/inactivo" 
                                           class="btn btn-danger btn-sm btn-confirm" 
                                           data-title="¿Desactivar a <?= $socio['nombre'] ?>?"
                                           title="Dar de Baja">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="/socios/cambiarEstado/<?= $socio['id'] ?>/activo" 
                                           class="btn btn-success btn-sm btn-confirm" 
                                           data-title="¿Reactivar a <?= $socio['nombre'] ?>?"
                                           title="Reactivar">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>

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