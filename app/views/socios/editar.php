<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Socio - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4><i class="fas fa-user-edit"></i> Editar Socio</h4>
                    </div>
                    <div class="card-body">
                        <form action="/socios/actualizar" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $socio['id'] ?>">
                            <input type="hidden" name="foto_actual" value="<?= $socio['foto'] ?>">

                            <div class="mb-4 text-center">
                                <label for="foto" class="form-label d-block fw-bold text-primary">Foto de Perfil</label>
                                <div class="p-3 bg-white border rounded">
                                    <?php if(!empty($socio['foto'])): ?>
                                        <div class="mb-3">
                                            <img src="/img/socios/<?= $socio['foto'] ?>" alt="Foto actual" 
                                                 class="img-thumbnail rounded-circle" width="120" height="120" 
                                                 style="object-fit: cover;">
                                            <p class="text-muted small mt-1">Foto Actual</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <label class="form-label small text-start w-100">Cambiar foto (Opcional):</label>
                                    <input type="file" class="form-control" name="foto" accept="image/png, image/jpeg, image/jpg">
                                    <small class="text-muted">Deja en blanco para mantener la foto actual. Formatos: JPG, PNG.</small>
                                </div>
                            </div>
                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre" value="<?= $socio['nombre'] ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">DNI</label>
                                    <input type="text" class="form-control" name="dni" value="<?= $socio['dni'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono" value="<?= $socio['telefono'] ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= $socio['email'] ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado">
                                    <option value="activo" <?= $socio['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="inactivo" <?= $socio['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    <option value="pendiente" <?= $socio['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/socios/index" class="btn btn-secondary me-md-2">Cancelar</a>
                                <button type="submit" class="btn btn-warning"><i class="fas fa-sync"></i> Actualizar Socio</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once '../app/views/inc/footer.php'; ?>
</body>
</html>