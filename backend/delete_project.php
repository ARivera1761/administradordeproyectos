<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit();
}

$db = new DbConfig();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Verificar que el proyecto le pertenezca al usuario
    $verificar = $conn->prepare("SELECT id FROM proyectos WHERE id = ? AND user_id = ?");
    $verificar->execute([$id, $user_id]);
    if ($verificar->rowCount() === 0) {
        header("Location: ../views/home.php?mensaje=no_autorizado");
        exit();
    }

    // Buscar todos los archivos asociados
    $stmtArchivos = $conn->prepare("SELECT ruta_archivo FROM archivos WHERE proyecto_id = ?");
    $stmtArchivos->execute([$id]);
    $archivos = $stmtArchivos->fetchAll(PDO::FETCH_ASSOC);

    // Eliminar archivos físicamente
    foreach ($archivos as $archivo) {
        $archivoPath = '../uploads/' . $archivo['ruta_archivo'];
        if (file_exists($archivoPath)) {
            unlink($archivoPath);
        }
    }

    // Eliminar registros de la tabla archivos
    $stmtDeleteArchivos = $conn->prepare("DELETE FROM archivos WHERE proyecto_id = ?");
    $stmtDeleteArchivos->execute([$id]);

    // Eliminar el proyecto
    $stmtProyecto = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
    $stmtProyecto->execute([$id]);

    header("Location: ../views/home.php?mensaje=eliminado");
    exit();
}
?>
