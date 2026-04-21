<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Socio - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-user-plus"></i> Registrar Nuevo Socio</h4>
                    </div>
                    <div class="card-body">
                        <form action="/socios/guardar" method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-4 text-center">
                                <label for="foto" class="form-label d-block fw-bold text-primary">Foto de Perfil</label>
                                <div class="p-3 bg-white border rounded">
                                    <input type="file" class="form-control" name="foto" accept="image/png, image/jpeg, image/jpg">
                                    <small class="text-muted">Se recomienda una foto cuadrada (JPG o PNG)</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dni" class="form-label">DNI / Identificación</label>
                                    <input type="text" class="form-control" name="dni" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado Inicial</label>
                                <select class="form-select" name="estado">
                                    <option value="activo">Activo</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/socios/index" class="btn btn-secondary me-md-2">Cancelar</a>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Socio</button>
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