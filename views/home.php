<?php
require_once '../backend/config.php';

$db = new DbConfig();
$conn = $db->getConnection();

$sql = "SELECT * FROM proyectos ORDER BY fecha_creacion DESC";
$stmt = $conn->query($sql);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ProjectsWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/stylehome.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-wave-square"></i> ProjectsWave</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="home.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container text-center">
            <h1 class="display-4">Bienvenido a ProjectsWave</h1>
            <p class="lead">Gestiona tus proyectos de manera eficiente y rápida.</p>
            <a href="crear_proyecto.html" class="btn btn-primary btn-lg">Crear Nuevo Proyecto</a>
        </div>
    </header>

    <section class="projects-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Mis Proyectos</h2>
            <div class="row">
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($proyecto['nombre']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                                <a href="#" class="btn btn-outline-primary">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2025 ProjectsWave. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
