<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        die(json_encode(["status" => "error", "message" => "Las contraseñas no coinciden"]));
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $dbConfig = new DbConfig();
        $conn = $dbConfig->getConnection();

        $sql = "INSERT INTO users (full_name, email, username, password, status) VALUES (:fullname, :email, :username, :password, 'Activo')";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente"]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error en el registro: " . $e->getMessage()]);
    }
}
?>
