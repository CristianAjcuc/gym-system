<?php
define('FPDF_FONTPATH', '../app/lib/fpdf/font/');
require_once '../app/lib/fpdf/fpdf.php';
require_once '../app/config/Database.php';
require_once '../app/models/Configuracion.php';

class ComprobanteController {
    
    private function verificarAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/index');
            exit;
        }
    }

    private function texto($str) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $str);
    }

    public function generar($id) {
        $this->verificarAuth();
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT s.*, so.nombre as socio, so.dni, so.email, p.nombre as plan, p.precio 
                  FROM suscripciones s
                  INNER JOIN socios so ON s.socio_id = so.id
                  INNER JOIN planes p ON s.plan_id = p.id
                  WHERE s.id = :id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$datos) { die("Error: El comprobante no existe."); }

        $configModel = new Configuracion();
        $empresa = $configModel->obtenerDatos();
        
        // --- VARIABLE MONEDA ---
        $simbolo = $empresa['moneda']; 

        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();

        // Logo y Encabezado
        $hay_logo = false;
        if (!empty($empresa['logo'])) {
            $ruta_logo = '../public/img/' . $empresa['logo'];
            if (file_exists($ruta_logo)) {
                $pdf->Image($ruta_logo, 10, 10, 25); 
                $hay_logo = true;
            }
        }

        $pdf->SetFont('Arial','B',16);
        if($hay_logo) { $pdf->Cell(25); } 
        $pdf->Cell(0, 10, $this->texto($empresa['nombre_sistema']), 0, 1, 'C');
        
        $pdf->SetFont('Arial','',10);
        if($hay_logo) { $pdf->Cell(25); }
        $pdf->Cell(0, 5, 'Comprobante de Pago', 0, 1, 'C');
        
        if($hay_logo) { $pdf->Cell(25); }
        $pdf->Cell(0, 5, $this->texto('RUC: ' . $empresa['ruc'] . ' - Tel: ' . $empresa['telefono']), 0, 1, 'C');

        if($hay_logo) { $pdf->Cell(25); }
        $pdf->Cell(0, 5, $this->texto($empresa['direccion']), 0, 1, 'C');
        
        $pdf->Ln(15);

        // Cliente
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(190, 10, 'Datos del Cliente', 1, 1, 'L'); 
        $pdf->SetFont('Arial','',11);
        
        $pdf->Cell(40, 8, 'Socio:', 0, 0);
        $pdf->Cell(100, 8, $this->texto($datos['socio']), 0, 1);
        $pdf->Cell(40, 8, 'DNI / Ident:', 0, 0);
        $pdf->Cell(100, 8, $datos['dni'], 0, 1);
        $pdf->Cell(40, 8, 'Email:', 0, 0);
        $pdf->Cell(100, 8, $this->texto($datos['email']), 0, 1);
        $pdf->Ln(5);

        // Detalle
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(190, 10, $this->texto('Detalle de la Suscripción'), 0, 1, 'L');
        $pdf->SetFillColor(220, 230, 241); 
        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(80, 10, $this->texto('Descripción'), 1, 0, 'L', true);
        $pdf->Cell(45, 10, 'Inicio - Fin', 1, 0, 'C', true);
        $pdf->Cell(45, 10, 'Importe', 1, 1, 'R', true);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(20, 10, $datos['id'], 1, 0, 'C');
        $pdf->Cell(80, 10, $this->texto("Plan: " . $datos['plan']), 1, 0, 'L');
        $fecha_txt = date('d/m', strtotime($datos['fecha_inicio'])) . " al " . date('d/m', strtotime($datos['fecha_fin']));
        $pdf->Cell(45, 10, $fecha_txt, 1, 0, 'C');
        
        // --- USO DE MONEDA DINÁMICA ---
        $pdf->Cell(45, 10, $simbolo . ' ' . number_format($datos['precio'], 2), 1, 1, 'R');

        // Totales
        $pdf->Ln(2);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(145, 10, 'TOTAL PAGADO:', 0, 0, 'R');
        $pdf->Cell(45, 10, $simbolo . ' ' . number_format($datos['precio'], 2), 1, 1, 'R');

        $pdf->Ln(25);
        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(0, 10, $this->texto('Gracias por su preferencia - Generado el ' . date('d/m/Y H:i')), 0, 1, 'C');
        $pdf->Cell(0, 5, $this->texto($empresa['email']), 0, 1, 'C');

        $pdf->Output('I', 'Comprobante_'.$datos['id'].'.pdf');
    }
}