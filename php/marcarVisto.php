<?php
session_name('camaras');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

// Conexión a la base de datos
require("connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Actualizar el estado de la notificación a "Visto" (1)
    $sql = "UPDATE solicitud SET visto = 1 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Redirigir a notificaciones.php con el id de la notificación para ver el detalle
    header("Location: notificaciones.php?id=$id");
    exit();
} else {
    // Si no hay un ID, redirige a las notificaciones de alguna forma
    header("Location: notificaciones.php");
    exit();
}
?>
