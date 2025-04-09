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
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $user_id = $_SESSION['user_id'];

    // Verificar que el proyecto pertenezca al usuario
    $verificar = $conn->prepare("SELECT id FROM proyectos WHERE id = ? AND user_id = ?");
    $verificar->execute([$id, $user_id]);
    if ($verificar->rowCount() === 0) {
        header("Location: ../views/home.php?mensaje=no_autorizado");
        exit();
    }

    // Actualizar nombre y descripción
    $sql = "UPDATE proyectos SET nombre = ?, descripcion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $id]);

    // Verificar si se subieron archivos nuevos
    if (!empty($_FILES['archivos']['name'][0])) {
        $total = count($_FILES['archivos']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['archivos']['error'][$i] === UPLOAD_ERR_OK) {
                $nombreArchivo = basename($_FILES['archivos']['name'][$i]);
                $rutaTemporal = $_FILES['archivos']['tmp_name'][$i];
                $destino = '../uploads/' . $nombreArchivo;

                if (move_uploaded_file($rutaTemporal, $destino)) {
                    $sqlInsertArchivo = "INSERT INTO archivos (proyecto_id, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)";
                    $stmtArchivo = $conn->prepare($sqlInsertArchivo);
                    $stmtArchivo->execute([$id, $nombreArchivo, $nombreArchivo]);
                }
            }
        }
    }

    header("Location: ../views/home.php?mensaje=editado");
    exit();
}
?>
