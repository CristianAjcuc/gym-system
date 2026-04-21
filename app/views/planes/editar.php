<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4>Editar Plan</h4>
                    </div>
                    <div class="card-body">
                        <form action="/planes/actualizar" method="POST">
                            <input type="hidden" name="id" value="<?= $plan['id'] ?>">

                            <div class="mb-3">
                                <label class="form-label">Nombre del Plan</label>
                                <input type="text" class="form-control" name="nombre" value="<?= $plan['nombre'] ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" step="0.01" class="form-control" name="precio" value="<?= $plan['precio'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duración (Días)</label>
                                    <input type="number" class="form-control" name="duracion" value="<?= $plan['duracion_dias'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"><?= $plan['descripcion'] ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Actualizar Plan</button>
                            <a href="/planes/index" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>