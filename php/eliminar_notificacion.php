<?php
require("connect.php"); // Asegúrate de incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        $sql = "DELETE FROM solicitud WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Notificación eliminada"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID inválido"]);
    }
}
?>
