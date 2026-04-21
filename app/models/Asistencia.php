<?php
class Asistencia {
    private $conn;
    private $table_name = "asistencias";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Registrar una nueva entrada
    public function registrar($socio_id) {
        $query = "INSERT INTO " . $this->table_name . " (socio_id) VALUES (:id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $socio_id);
        return $stmt->execute();
    }

    // Ver quiénes han entrado hoy (para mostrar en la pantalla)
    public function obtenerDeHoy() {
        $hoy = date('Y-m-d');
        // Traemos también el nombre del socio y la hora
        $query = "SELECT a.fecha_hora, s.nombre, s.dni 
                  FROM " . $this->table_name . " a
                  INNER JOIN socios s ON a.socio_id = s.id
                  WHERE DATE(a.fecha_hora) = :hoy
                  ORDER BY a.fecha_hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hoy', $hoy);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}