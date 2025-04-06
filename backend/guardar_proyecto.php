<?php
// guardar_proyecto.php
session_start();

// Incluir la configuración de la base de datos y la clase Proyecto
include_once __DIR__ . '/config.php'; // Usamos __DIR__ para obtener la ruta absoluta
include_once __DIR__ . '/proyecto.php';

// Crear una instancia de Database y obtener la conexión
$database = new Database();
$db = $database->getConnection();

// Crear una instancia de Proyecto
$proyecto = new Proyecto($db);

// Obtener datos del formulario
$proyecto->nombre = $_POST['nombre'];
$proyecto->descripcion = $_POST['descripcion'];

// Crear el proyecto
if ($proyecto->crear()) {
    $_SESSION['mensaje'] = "Proyecto creado exitosamente.";
} else {
    $_SESSION['mensaje'] = "Error al crear el proyecto.";
}

// Redirigir al usuario
header("Location: home.html"); // Asegúrate de que home.html esté en la raíz del proyecto
exit();
?>