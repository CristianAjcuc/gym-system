<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Plan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4>Nuevo Plan</h4>
                    </div>
                    <div class="card-body">
                        <form action="/planes/guardar" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Plan</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej: Mensual VIP" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" step="0.01" class="form-control" name="precio" placeholder="0.00" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duración (Días)</label>
                                    <input type="number" class="form-control" name="duracion" placeholder="Ej: 30" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Guardar Plan</button>
                            <a href="/planes/index" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>