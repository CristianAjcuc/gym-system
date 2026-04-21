<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Gasto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php require_once '../app/views/inc/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4>Registrar Salida de Dinero</h4>
                    </div>
                    <div class="card-body">
                        <form action="/gastos/guardar" method="POST">
                            <div class="mb-3">
                                <label>Descripción del Gasto</label>
                                <input type="text" name="descripcion" class="form-control" placeholder="Ej: Recibo de Luz, Pago Limpieza..." required>
                            </div>
                            <div class="mb-3">
                                <label>Monto</label>
                                <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="mb-3">
                                <label>Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Guardar Gasto</button>
                            <a href="/gastos/index" class="btn btn-secondary w-100 mt-2">Cancelar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>