<?php

session_start(); // Asegúrate de iniciar la sesión antes de acceder a las variables de sesión.

require_once 'config.php';

class Auth {

    private $db;

    public function __construct() {
        $dbConfig = new DbConfig();
        $this->db = $dbConfig->getConnection();
    }

    public function getDb() {
        return $this->db;
    }

    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                if ($row['status'] === 'Activo') {
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['nombre'] = $row['full_name'];
                    $_SESSION['email'] = $row['email'];

                    return true; // Autenticación exitosa
                } else {
                    return 'inactive'; // Usuario inactivo
                }
            } else {
                return 'invalid'; // Credenciales inválidas
            }
        } else {
            return 'invalid'; // Usuario no encontrado
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $auth = new Auth();
    $result = $auth->authenticate($username, $password);

    if ($result === true) {
        $response = array(
            'status' => 'success',
            'user_id' => $_SESSION['user_id'],
            'nombre' => $_SESSION['nombre'],
            'email' => $_SESSION['email']
        );
    } elseif ($result === 'inactive') {
        $response = array(
            'status' => 'error',
            'message' => 'El usuario está inactivo'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Verifique la información ingresada'
        );
    }

    echo json_encode($response);
}
?>
