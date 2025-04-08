<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (!empty($nombre)) {
        $db = new DbConfig();
        $conn = $db->getConnection();

        $sql = "INSERT INTO proyectos (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);

        if ($stmt->execute()) {
            header("Location: ../views/home.php");
            exit();
        } else {
            echo "Error al guardar el proyecto.";
        }
    } else {
        echo "El nombre del proyecto es obligatorio.";
    }
}
?>
