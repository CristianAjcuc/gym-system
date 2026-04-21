<?php
require_once '../app/models/Asistencia.php';
require_once '../app/models/Socio.php';
require_once '../app/models/Suscripcion.php';

class AsistenciaController {

    private function verificarAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/index');
            exit;
        }
    }

    public function index() {
        $this->verificarAuth();
        // Cargar historial del día para la tabla inferior
        $asistenciaModel = new Asistencia();
        $historial = $asistenciaModel->obtenerDeHoy();
        require_once '../app/views/asistencia/index.php';
    }

    // PASO 1: VALIDAR Y MOSTRAR PERFIL (SIN GUARDAR)
    public function validar() {
        $this->verificarAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = $_POST['dni'];
            
            $perfil_validacion = null; // Datos para la vista
            $error_busqueda = "";

            $db = new Database();
            $conn = $db->getConnection();

            // 1. Buscar socio (ID, Nombre, Estado, Foto)
            $querySocio = "SELECT id, nombre, estado, foto FROM socios WHERE dni = :dni LIMIT 1";
            $stmt = $conn->prepare($querySocio);
            $stmt->bindParam(':dni', $dni);
            $stmt->execute();
            $socio = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$socio) {
                $error_busqueda = "❌ DNI no encontrado en la base de datos.";
            } else {
                // 2. Verificar suscripción activa
                $hoy = date('Y-m-d');
                $querySub = "SELECT * FROM suscripciones 
                             WHERE socio_id = :id 
                             AND estado = 'activa' 
                             AND fecha_fin >= :hoy 
                             LIMIT 1";
                
                $stmtSub = $conn->prepare($querySub);
                $stmtSub->bindParam(':id', $socio['id']);
                $stmtSub->bindParam(':hoy', $hoy);
                $stmtSub->execute();
                
                $tiene_acceso = false;
                $dias_restantes = 0;
                $vencimiento = "";

                if ($stmtSub->rowCount() > 0) {
                    $tiene_acceso = true;
                    $sub = $stmtSub->fetch(PDO::FETCH_ASSOC);
                    $diasRestantes = (strtotime($sub['fecha_fin']) - strtotime($hoy)) / (60 * 60 * 24);
                    $vencimiento = date('d/m/Y', strtotime($sub['fecha_fin']));
                }

                // Preparamos los datos para que la Vista los muestre (NO GUARDAMOS AÚN)
                $perfil_validacion = [
                    'id' => $socio['id'],
                    'nombre' => $socio['nombre'],
                    'foto' => $socio['foto'],
                    'estado_socio' => $socio['estado'],
                    'tiene_acceso' => $tiene_acceso, // Booleano (True/False)
                    'dias_restantes' => round($diasRestantes),
                    'fecha_vence' => $vencimiento
                ];
            }

            // Mantenemos el historial visible
            $asistenciaModel = new Asistencia();
            $historial = $asistenciaModel->obtenerDeHoy();
            
            require_once '../app/views/asistencia/index.php';
        }
    }

    // PASO 2: REGISTRAR EL INGRESO (ACCIÓN DEL OPERADOR)
    public function registrar() {
        $this->verificarAuth();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $socio_id = $_POST['socio_id'];
            
            // AHORA SÍ: Guardar en la base de datos
            $asistenciaModel = new Asistencia();
            if($asistenciaModel->registrar($socio_id)) {
                $mensaje_exito = "✅ Ingreso registrado correctamente.";
            } else {
                $error_busqueda = "Error al guardar la asistencia.";
            }

            // Recargar historial actualizado
            $historial = $asistenciaModel->obtenerDeHoy();
            
            require_once '../app/views/asistencia/index.php';
        }
    }
}