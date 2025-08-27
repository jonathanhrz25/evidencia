<?php

$server = 'localhost';
$username = 'root';
$password = '';
$database = 'camaras';

// Verificar si PDO está disponible
if (extension_loaded('pdo_mysql')) {
    try {
        // Conexión PDO
        $conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Conexión exitosa con PDO";
    } catch (PDOException $e) {
        die('Conexión fallida con PDO: ' . $e->getMessage());
    }
}
// Verificar si MySQLi está disponible
elseif (extension_loaded('mysqli')) {
    // Conexión MySQLi
    $conn = new mysqli($server, $username, $password, $database);
    if ($conn->connect_error) {
        die('Conexión fallida con MySQLi: ' . $conn->connect_error);
    }
    // echo "Conexión exitosa con MySQLi";
} else {
    die("No se ha encontrado ninguna extensión compatible de MySQL.");
}

?>
