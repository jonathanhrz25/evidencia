<?php
session_name('camaras');
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autenticado.']);
    exit();
}

// Conectar a la base de datos
$conn = new mysqli("localhost", "root", "", "camaras");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error en la conexión a la base de datos.']));  
}

// Verificar si se han recibido archivos y datos requeridos
if (isset($_FILES['media']) && isset($_POST['Id_solicitud']) && isset($_POST['usuario'])) {
    $Id_solicitud = $_POST['Id_solicitud'];
    $usuario = $_POST['usuario'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Configuración de la carpeta de destino
    $uploadDirectory = "uploads/";

    // Crear la carpeta 'uploads' si no existe
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Procesar cada archivo subido
    $files = $_FILES['media'];
    $uploadedFiles = [];

    foreach ($files['name'] as $index => $fileName) {
        $fileTmpName = $files['tmp_name'][$index];

        // Generar un nombre único para el archivo
        $uniqueFileName = uniqid() . "_" . basename($fileName);
        $targetFilePath = $uploadDirectory . $uniqueFileName;

        // Mover el archivo al directorio de almacenamiento
        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            // Insertar en la base de datos
            $sql = "INSERT INTO videos (Id_solicitud, usuario, descripcion, video) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $Id_solicitud, $usuario, $description, $uniqueFileName);

            if ($stmt->execute()) {
                $uploadedFiles[] = $uniqueFileName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo.']);
            exit();
        }
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Archivos subidos y registrados correctamente.',
        'files' => $uploadedFiles
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos o archivos.']);
}
?>
