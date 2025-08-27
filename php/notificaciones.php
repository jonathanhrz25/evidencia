<?php
session_name('camaras');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

// Verificar si el rol es el adecuado (TI)
if ($_SESSION['rol'] !== 'TI') {
    header("Location: ../inicioSesion.php");
    exit();
}

// Conexión a la base de datos
require("connect.php");

// Verificar si se pasó el id de la notificación
if (isset($_GET['id'])) {
    $notificacion_id = $_GET['id'];

    // Consulta para obtener los detalles de la notificación
    $sql = "SELECT id, descripcion, cedis, fecha, hora_inicio, hora_fin, correo, fecha_hoy, notificacion, visto, usuario_solicitante FROM solicitud WHERE id = :notificacion_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':notificacion_id', $notificacion_id);
    $stmt->execute();
    $notificacion = $stmt->fetch();

    if ($notificacion) {
        // Si la notificación existe, actualizar el estado "visto" si es necesario
        if ($notificacion['visto'] == 0) {
            $update_sql = "UPDATE solicitud SET visto = 1 WHERE id = :notificacion_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':notificacion_id', $notificacion_id);
            $update_stmt->execute();
        }
    } else {
        // Si no se encuentra la notificación
        header("Location: principal.php");
        exit();
    }
} else {
    // Si no se pasa un id
    header("Location: principal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Detalles de la Notificación</title>
</head>

<body>

    <header>
        <nav class="navbar navbar-dark bg-dark fixed-top"
            style="background-color: #081856!important; text-align: left;">
            <div class="container-fluid">
                <a class="navbar-brand text-white">
                    <img src="../img/loguito2.png" alt="" height="45" class="d-inline-block align-text-top">
                </a>
            </div>
        </nav>
    </header>
    <div style="height: 40px;"></div>

    <div class="container mt-5">
        <h1 class="display-5 text-center">Detalles de la Notificación</h1><br>

        <div class="card">
            <div class="card-header">
                <h4>Descripción</h4>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> <?php echo $notificacion['id']; ?></p>
                <p><strong>Usuario:</strong> <?php echo $notificacion['usuario_solicitante']; ?></p>
                <p><strong>Descripción:</strong> <?php echo $notificacion['descripcion']; ?></p>
                <p><strong>CEDIS:</strong> <?php echo $notificacion['cedis']; ?></p>
                <p><strong>Fecha del video solicitado:</strong> <?php echo $notificacion['fecha']; ?></p>
                <p><strong>Hora Inicio del video de Cámaras:</strong> <?php echo $notificacion['hora_inicio']; ?></p>
                <p><strong>Hora Fin del video de Cámaras:</strong> <?php echo $notificacion['hora_fin']; ?></p>
                <p><strong>Correo Destinatario:</strong> <?php echo $notificacion['correo']; ?></p>
                <p><strong>Fecha de creación de Solicitud:</strong> <?php echo $notificacion['fecha_hoy']; ?></p>
                <p><strong>Estado:</strong>
                    <?php echo $notificacion['visto'] == 1 ? 'Visto' : 'No Visto'; ?>
                </p>
            </div>
        </div>

        <a href="principal.php" class="btn btn-primary mt-3">Volver a la página principal</a>

        <a href="principal.php?id=<?php echo $notificacion['id']; ?>&usuario=<?php echo urlencode($notificacion['usuario_solicitante']); ?>"class="btn btn-success mt-3">Responder a solicitud</a>
    </div><br><br><br>
</body>

<?php include '../css/footer.php'; ?>

</html>