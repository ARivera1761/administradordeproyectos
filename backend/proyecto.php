<?php
class Proyecto {
    private $conn;
    private $table_name = "proyectos";

    public $id;
    public $nombre;
    public $descripcion;
    public $fecha_creacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear un proyecto
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        // Bind parameters
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);

        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>