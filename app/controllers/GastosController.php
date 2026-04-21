<?php
require_once '../app/models/Gasto.php';

class GastosController {

    private function verificarAuth() {
        // 1. Verificar si está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/index');
            exit;
        }
        // 2. BLOQUEO DE ROL: Solo Admin puede entrar aquí
        if ($_SESSION['user_rol'] != 'admin') {
            header('Location: /home/index'); // Lo expulsamos al inicio
            exit;
        }
    }

    public function index() {
        $this->verificarAuth();
        $gastoModel = new Gasto();
        $stmt = $gastoModel->obtenerTodos();
        $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../app/views/gastos/index.php';
    }

    public function crear() {
        $this->verificarAuth();
        require_once '../app/views/gastos/crear.php';
    }

    public function guardar() {
        $this->verificarAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'descripcion' => $_POST['descripcion'],
                'monto' => $_POST['monto'],
                'fecha' => $_POST['fecha']
            ];

            $gastoModel = new Gasto();
            if ($gastoModel->agregar($datos)) {
                header('Location: /gastos/index');
            }
        }
    }

    public function eliminar($id) {
        $this->verificarAuth();
        $gastoModel = new Gasto();
        $gastoModel->eliminar($id);
        header('Location: /gastos/index');
    }
}