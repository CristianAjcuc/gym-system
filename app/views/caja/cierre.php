<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja - Iron Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <?php require_once '../app/views/inc/navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h3 class="fw-bold"><i class="fas fa-file-invoice-dollar"></i> CORTE DE CAJA</h3>
                        <p class="m-0">Cajero: <?= $_SESSION['user_name'] ?> | Apertura: <?= date('d/m/Y H:i', strtotime($cajaAbierta['fecha_apertura'])) ?></p>
                    </div>
                    
                    <div class="card-body p-5">
                        
                        <div class="row text-center mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-light h-100">
                                    <small class="text-muted text-uppercase fw-bold">Base Inicial</small>
                                    <h3 class="text-secondary mt-2"><?= $config['moneda'] ?> <?= number_format($monto_inicial, 2) ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-success bg-opacity-10 border-success h-100">
                                    <small class="text-success text-uppercase fw-bold">Ventas Turno (+)</small>
                                    <h3 class="text-success mt-2"><?= $config['moneda'] ?> <?= number_format($total_ventas, 2) ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded bg-danger bg-opacity-10 border-danger h-100">
                                    <small class="text-danger text-uppercase fw-bold">Gastos Turno (-)</small>
                                    <h3 class="text-danger mt-2"><?= $config['moneda'] ?> <?= number_format($total_gastos, 2) ?></h3>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary text-center py-4 mb-4 shadow-sm">
                            <h5 class="m-0 text-uppercase text-primary">Dinero esperado en Cajón</h5>
                            <h1 class="fw-bold display-3 text-dark my-2"><?= $config['moneda'] ?> <?= number_format($saldo_esperado, 2) ?></h1>
                            <small class="text-muted">(Base Inicial + Ventas - Gastos)</small>
                        </div>

                        <hr class="my-4">

                        <form action="/caja/cerrar" method="POST" class="needs-validation" id="formCierreCaja">
                            
                            <input type="hidden" name="caja_id" value="<?= $cajaAbierta['id'] ?>">
                            <input type="hidden" name="total_ventas" value="<?= $total_ventas ?>">
                            <input type="hidden" name="total_gastos" value="<?= $total_gastos ?>">
                            <input type="hidden" name="saldo_esperado" value="<?= $saldo_esperado ?>">

                            <div class="mb-4 text-center">
                                <label class="form-label fw-bold fs-4 text-dark mb-3">¿Cuánto dinero hay FÍSICAMENTE?</label>
                                <div class="d-flex justify-content-center">
                                    <div class="input-group input-group-lg w-75">
                                        <span class="input-group-text bg-warning text-dark fw-bold">CONTEO REAL: <?= $config['moneda'] ?></span>
                                        <input type="number" step="0.01" name="monto_fisico" class="form-control fw-bold text-center fs-3" placeholder="0.00" required>
                                    </div>
                                </div>
                                <div class="form-text mt-2">Cuente los billetes y monedas del cajón e ingrese el total. El sistema calculará si hay faltante o sobrante.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-danger btn-lg py-3 shadow"><i class="fas fa-lock"></i> CERRAR CAJA Y FINALIZAR TURNO</button>
                                <a href="/home/index" class="btn btn-outline-secondary">Volver al Dashboard</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require_once '../app/views/inc/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formCierre = document.getElementById('formCierreCaja');
            
            formCierre.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene el envío automático del formulario

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Confirma que has contado el dinero correctamente. Se procederá a cerrar el turno y esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Color rojo peligro (coincide con el botón)
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cerrar turno',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true // Pone el botón de cancelar a la izquierda
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, enviamos el formulario programáticamente
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>