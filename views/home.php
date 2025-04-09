<?php
require_once '../backend/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit();
}

$db = new DbConfig();
$conn = $db->getConnection();
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM proyectos WHERE user_id = ? ORDER BY fecha_creacion DESC");
$stmt->execute([$user_id]);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener archivos adjuntos agrupados por proyecto
$archivosStmt = $conn->query("SELECT * FROM archivos");
$archivosPorProyecto = [];
while ($archivo = $archivosStmt->fetch(PDO::FETCH_ASSOC)) {
    $archivosPorProyecto[$archivo['proyecto_id']][] = $archivo;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ProjectsWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/stylehome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .alert-fixed {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            width: 90%;
            max-width: 600px;
        }
    </style>
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

<?php if (isset($_GET['mensaje'])): ?>
    <?php
        $tipo = '';
        $texto = '';
        if ($_GET['mensaje'] == 'eliminado') {
            $tipo = 'success';
            $texto = 'Proyecto eliminado exitosamente.';
        } elseif ($_GET['mensaje'] == 'editado') {
            $tipo = 'info';
            $texto = 'Proyecto editado correctamente.';
        } elseif ($_GET['mensaje'] == 'creado') {
            $tipo = 'success';
            $texto = 'Proyecto creado exitosamente.';
        }
    ?>
    <?php if ($texto): ?>
        <div class="alert alert-<?= $tipo ?> alert-dismissible fade show alert-fixed" role="alert">
            <?= $texto ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<section class="projects-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Mis Proyectos</h2>
        <div class="row">
            <?php foreach ($proyectos as $proyecto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow position-relative">
                        <div class="position-absolute top-0 end-0 p-2 d-flex gap-2">
                            <button class="btn btn-sm btn-warning" title="Editar" data-bs-toggle="modal" 
                                data-bs-target="#editModal" 
                                data-id="<?= $proyecto['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($proyecto['nombre']) ?>" 
                                data-descripcion="<?= htmlspecialchars($proyecto['descripcion']) ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" title="Eliminar" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $proyecto['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($proyecto['nombre']) ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detalleModal<?= $proyecto['id'] ?>">Ver Detalles</button>
                        </div>
                    </div>
                </div>

                <!-- Modal Detalles Proyecto -->
                <div class="modal fade" id="detalleModal<?= $proyecto['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalles del Proyecto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <h5><?= htmlspecialchars($proyecto['nombre']) ?></h5>
                                <p><?= nl2br(htmlspecialchars($proyecto['descripcion'])) ?></p>
                                <?php if (!empty($archivosPorProyecto[$proyecto['id']])): ?>
                                    <ul>
                                        <?php foreach ($archivosPorProyecto[$proyecto['id']] as $archivo): ?>
                                            <li><a href="../uploads/<?= htmlspecialchars($archivo['nombre_archivo']) ?>" target="_blank">📎 <?= htmlspecialchars($archivo['nombre_archivo']) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">Sin archivos adjuntos.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Modal Confirmar Eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="../backend/delete_project.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar este proyecto?
        <input type="hidden" name="id" id="delete-id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Proyecto -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="../backend/update_project.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label for="edit-nombre" class="form-label">Nombre del Proyecto</label>
                    <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="edit-descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="edit-descripcion" name="descripcion" rows="4"></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit-archivo" class="form-label">Archivos adjuntos</label>
                    <input type="file" class="form-control" id="edit-archivo" name="archivos[]" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<footer class="bg-dark text-white text-center py-4">
    <p>&copy; 2025 ProjectsWave. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const descripcion = button.getAttribute('data-descripcion');

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-nombre').value = nombre;
        document.getElementById('edit-descripcion').value = descripcion;
    });

    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        document.getElementById('delete-id').value = id;
    });
</script>
</body>
</html>
