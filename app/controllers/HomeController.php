<?php
require_once '../app/models/Dashboard.php';

class HomeController {
    
    public function index() {
        // --- Seguridad ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/index');
            exit;
        }

        $dashboardModel = new Dashboard();

        // 1. Obtener KPIs Financieros
        $totalSocios = $dashboardModel->contarSociosActivos();
        $ingresosMes = $dashboardModel->ingresosEsteMes();
        $gastosMes   = $dashboardModel->egresosEsteMes(); // Nuevo
        
        // CÁLCULO DE UTILIDAD NETA
        $utilidad = $ingresosMes - $gastosMes;

        // 2. Obtener lista de Vencimientos
        $vencimientos = $dashboardModel->obtenerVencimientosCercanos();

        // 3. Datos Gráfico Barras
        $datosGrafico = $dashboardModel->ventasUltimosMeses();
        $labels = [];
        $data = [];
        foreach($datosGrafico as $g) {
            $labels[] = $g['mes'];
            $data[] = $g['cantidad'];
        }

        // 4. Datos Gráfico Pastel
        $datosPlanes = $dashboardModel->ventasPorPlan();
        $planesLabels = [];
        $planesData = [];
        foreach($datosPlanes as $p) {
            $planesLabels[] = $p['nombre'];
            $planesData[] = $p['cantidad'];
        }

        require_once '../app/views/home/index.php';
    }
}