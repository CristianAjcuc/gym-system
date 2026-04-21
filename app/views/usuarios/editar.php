<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require_once '../app/views/inc/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4>Editar Usuario</h4>
                    </div>
                    <div class="card-body">
                        <form action="/usuarios/actualizar" method="POST">
                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                            
                            <div class="mb-3">
                                <label>Nombre Completo</label>
                                <input type="text" name="nombre" class="form-control" value="<?= $usuario['nombre'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?= $usuario['email'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                            </div>
                            <div class="mb-3">
                                <label>Rol</label>
                                <select name="rol" class="form-select">
                                    <option value="recepcionista" <?= $usuario['rol']=='recepcionista'?'selected':'' ?>>Recepcionista</option>
                                    <option value="entrenador" <?= $usuario['rol']=='entrenador'?'selected':'' ?>>Entrenador</option>
                                    <option value="admin" <?= $usuario['rol']=='admin'?'selected':'' ?>>Administrador</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">Actualizar Usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>