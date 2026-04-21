<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h3><i class="fas fa-user-shield"></i> Usuarios del Sistema</h3>
                <a href="/usuarios/crear" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo Usuario</a>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered table-data align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= $u['nombre'] ?></td>
                            <td><?= $u['email'] ?></td>
                            <td>
                                <?php 
                                    $colorRol = 'secondary';
                                    if($u['rol'] == 'admin') $colorRol = 'danger';
                                    if($u['rol'] == 'recepcionista') $colorRol = 'primary';
                                    if($u['rol'] == 'entrenador') $colorRol = 'success';
                                ?>
                                <span class="badge bg-<?= $colorRol ?> text-uppercase"><?= $u['rol'] ?></span>
                            </td>
                            <td>
                                <?php if($u['estado'] == 'activo'): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/usuarios/editar/<?= $u['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                    
                                    <?php if($u['estado'] == 'activo'): ?>
                                        <a href="/usuarios/cambiarEstado/<?= $u['id'] ?>/inactivo" 
                                           class="btn btn-danger btn-sm btn-confirm"
                                           data-title="¿Bloquear acceso a <?= $u['nombre'] ?>?"
                                           title="Desactivar cuenta">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="/usuarios/cambiarEstado/<?= $u['id'] ?>/activo" 
                                           class="btn btn-success btn-sm btn-confirm"
                                           data-title="¿Reactivar acceso a <?= $u['nombre'] ?>?"
                                           title="Activar cuenta">
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