
<?php
require_once 'config.php';
session_start();

$db = new DbConfig();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $user_id = $_SESSION['user_id'];

    if (!empty($_FILES['archivos']['name'][0])) {
        $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_creacion, user_id) VALUES (?, ?, NOW(), ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $descripcion, $user_id]);
        $proyecto_id = $conn->lastInsertId();

        foreach ($_FILES['archivos']['tmp_name'] as $index => $tmpName) {
            $archivo_nombre = basename($_FILES['archivos']['name'][$index]);
            $destino = '../uploads/' . $archivo_nombre;
            if (move_uploaded_file($tmpName, $destino)) {
                $sqlArchivo = "INSERT INTO archivos (proyecto_id, nombre_archivo) VALUES (?, ?)";
                $stmtArchivo = $conn->prepare($sqlArchivo);
                $stmtArchivo->execute([$proyecto_id, $archivo_nombre]);
            }
        }
    }

    header("Location: ../views/home.php?mensaje=creado");
    exit();
}
?>
