<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. Inicializar DataTables en cualquier tabla con la clase "table-data"
            $('.table-data').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 5, // Mostrar 5 registros por página por defecto
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]]
            });

            // 2. Lógica para SweetAlert al Eliminar/Desactivar
            $('.btn-confirm').on('click', function(e) {
                e.preventDefault(); // Detener el enlace
                const href = $(this).attr('href'); // Obtener la ruta
                const title = $(this).data('title') || '¿Estás seguro?';

                Swal.fire({
                    title: title,
                    text: "Esta acción cambiará el estado del registro.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href; // Redirigir si confirma
                    }
                })
            });
        });
    </script>
</body>
</html>