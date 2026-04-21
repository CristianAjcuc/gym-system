<?php
class Socio {
    private $conn;
    private $table_name = "socios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar todos los socios
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener un socio por ID (para editar)
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insertar nuevo socio (Incluye Foto)
    public function agregar($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre, dni, email, telefono, estado, foto) 
                 VALUES (:nombre, :dni, :email, :telefono, :estado, :foto)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":dni", $datos['dni']);
        $stmt->bindParam(":email", $datos['email']);
        $stmt->bindParam(":telefono", $datos['telefono']);
        $stmt->bindParam(":estado", $datos['estado']);
        $stmt->bindParam(":foto", $datos['foto']); 

        return $stmt->execute();
    }

    // --- CORRECCIÓN IMPORTANTE AQUÍ ---
    // Actualizar datos del socio (Incluyendo la Foto)
    public function actualizar($datos) {
        $query = "UPDATE " . $this->table_name . " 
                 SET nombre = :nombre, dni = :dni, email = :email, 
                     telefono = :telefono, estado = :estado, foto = :foto 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $datos['nombre']);
        $stmt->bindParam(":dni", $datos['dni']);
        $stmt->bindParam(":email", $datos['email']);
        $stmt->bindParam(":telefono", $datos['telefono']);
        $stmt->bindParam(":estado", $datos['estado']);
        $stmt->bindParam(":foto", $datos['foto']); // <--- ¡Esto faltaba para guardar el cambio!
        $stmt->bindParam(":id", $datos['id']);

        return $stmt->execute();
    }

    // Cambiar estado (Activar/Desactivar)
    public function cambiarEstado($id, $estado) {
        $query = "UPDATE " . $this->table_name . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
}