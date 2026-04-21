<?php
define('FPDF_FONTPATH', '../app/lib/fpdf/font/');
// CAMBIO 1: Cargamos la nueva librería de QR
require_once '../app/lib/fpdf/qrcode.php'; 
require_once '../app/config/Database.php';
require_once '../app/models/Configuracion.php';
require_once '../app/models/Socio.php';

class CarnetController {
    
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
        $socioModel = new Socio();
        $socio = $socioModel->obtenerPorId($id);
        if (!$socio) { die("Socio no encontrado"); }

        $configModel = new Configuracion();
        $config = $configModel->obtenerDatos();

        // CAMBIO 2: Usamos la clase PDF_QR
        // Tamaño Tarjeta: 85mm x 55mm
        $pdf = new PDF_QR('L', 'mm', [85, 55]);
        $pdf->SetMargins(2, 2, 2);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // --- MARCO ---
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(50, 50, 50);
        $pdf->Rect(1, 1, 83, 53);

        // --- ENCABEZADO ---
        if (!empty($config['logo'])) {
            $ruta_logo = '../public/img/' . $config['logo'];
            if (file_exists($ruta_logo)) {
                $pdf->Image($ruta_logo, 3, 3, 10); // Logo un poco más pequeño
            }
        }

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(14, 4);
        $pdf->Cell(68, 5, $this->texto($config['nombre_sistema']), 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetXY(14, 9);
        $pdf->Cell(68, 3, 'CARNET DIGITAL DE SOCIO', 0, 1, 'C');

        // --- SECCIÓN IZQUIERDA: FOTO ---
        $y_elementos = 16;
        $tamano_foto = 24; // Foto cuadrada de 24x24

        if (!empty($socio['foto'])) {
            $ruta_foto = '../public/img/socios/' . $socio['foto'];
            if (file_exists($ruta_foto)) {
                $pdf->Image($ruta_foto, 4, $y_elementos, $tamano_foto, $tamano_foto);
                $pdf->Rect(4, $y_elementos, $tamano_foto, $tamano_foto);
            }
        } else {
            $pdf->Rect(4, $y_elementos, $tamano_foto, $tamano_foto);
            $pdf->SetXY(4, $y_elementos + 10);
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell($tamano_foto, 4, 'SIN FOTO', 0, 0, 'C');
        }

        // --- SECCIÓN CENTRAL: DATOS ---
        $x_datos = 30;
        $pdf->SetXY($x_datos, $y_elementos);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 4, 'SOCIO:', 0, 1);
        
        $pdf->SetXY($x_datos, $y_elementos + 4);
        $pdf->SetFont('Arial', '', 8);
        $nombre_corto = substr($socio['nombre'], 0, 20);
        $pdf->Cell(30, 4, $this->texto($nombre_corto), 0, 1);

        $pdf->SetXY($x_datos, $y_elementos + 10);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(30, 4, 'DNI / ID:', 0, 1);

        $pdf->SetXY($x_datos, $y_elementos + 14);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(30, 4, $socio['dni'], 0, 1);

        // --- SECCIÓN DERECHA: CÓDIGO QR ---
        // Posicionamos el QR a la derecha de los datos
        // X = 60, Y = 16, Tamaño = 24x24 (igual que la foto)
        
        // CAMBIO 3: Llamamos a la función ImageQR
        // ImageQR(texto, x, y, ancho, alto)
        $pdf->ImageQR($socio['dni'], 60, $y_elementos, 22, 22);

        // Pie de página pequeño con el DNI
        $pdf->SetXY(0, 49);
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetTextColor(100);
        $pdf->Cell(85, 3, 'ID: ' . $socio['dni'], 0, 0, 'C');

        $pdf->Output('I', 'Carnet_QR_'.$socio['dni'].'.pdf');
    }
}