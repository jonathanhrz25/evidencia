<?php
session_name('camaras'); // Asegúrate de tener el mismo nombre de sesión para este proyecto
session_start();
require 'connect.php';

// Verifica si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    // Intentamos obtener los datos desde la tabla de usuariosTI
    $records = $conn->prepare('SELECT id, usuario, password FROM usuariosti WHERE id = :id');
    $records->bindParam(':id', $_SESSION['user_id']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra en usuariosTI, buscamos en usuariosc
    if (empty($results)) {
        $records = $conn->prepare('SELECT id, usuario, password FROM usuariosc WHERE id = :id');
        $records->bindParam(':id', $_SESSION['user_id']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);
    }

    // Si encontramos el usuario, mostramos el mensaje de confirmación de cierre de sesión
    if ($results) {
        $user = $results;
    }
}

// Si se envía la confirmación de cierre de sesión
if (isset($_POST['confirm_logout'])) {
    session_unset(); // Limpia todas las variables de sesión
    session_destroy(); // Destruye la sesión
    header('Location: ./inicioSesion.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/dark.css">
    <title>Sistemas Camaras</title>
    <style>
        .custom-btn-color {
            background-color: #003eaf !important;
            color: white !important;
            border-color: #003eaf !important;
        }

        .custom-btn-color:hover {
            background-color: #14d6e0 !important;
            border-color: #14d6e0 !important;
        }

        .custom-btn-color:active {
            background-color: #04123b !important;
            border-color: #04123b !important;
        }

        input[type="submit"].custom-btn-color {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="text-center">
    <?php if (!empty($user)): ?>
        <br> Usuario: <?= htmlspecialchars($user['usuario']); ?>
        <br>¿Está seguro de que quiere cerrar sesión?<br><br>

        <form method="POST">
            <input type="hidden" name="confirm_logout" value="1">
            <button type="submit" class="btn btn-info custom-btn-color">Cerrar Sesión</button>
        </form>
    <?php else: ?>
        <p>No hay usuario logueado.</p>
    <?php endif; ?>
</div>

<script src="../js/main.js"></script>

</body>
</html>

<?php include '../css/footer.php'; ?>
