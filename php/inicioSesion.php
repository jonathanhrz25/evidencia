<?php include '../css/header.php' ?>

<?php
session_name('camaras'); // Asegúrate de tener el mismo nombre de sesión para el proyecto
session_start();
require 'connect.php';

$results = null;

if (isset($_SESSION['user_id'])) {
    header("Location: ./principal.php");
    exit();
}

if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
    // Verificamos en la tabla usuariosti (Usuarios de sistemas)
    $records = $conn->prepare('SELECT * FROM usuariosti WHERE usuario = :usuario');
    $records->bindParam(':usuario', $_POST['usuario']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    
    // Si no lo encontramos en usuariosti, buscamos en usuariosc (Usuarios de mesas)
    if (empty($results)) {
        $records = $conn->prepare('SELECT * FROM usuariosc WHERE usuario = :usuario');
        $records->bindParam(':usuario', $_POST['usuario']);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);
    }

    $message = '';

    if (!empty($results) && password_verify($_POST['password'], $results['password'])) {
        $_SESSION['user_id'] = $results['id'];
        $_SESSION['usuario'] = $results['usuario']; // Asigna el nombre de usuario a $_SESSION['usuario']
        $_SESSION['rol'] = $results['rol']; // Guardamos el rol en la sesión

        // Aquí determinamos en qué tabla está el usuario para redirigirlo correctamente
        if ($records->queryString === 'SELECT * FROM usuariosti WHERE usuario = :usuario') {
            // Si el usuario fue encontrado en la tabla 'usuariosti' (Usuarios de sistemas)
            header("Location: ../php/principal.php"); // Redirige a la página de usuariosTI
        } else {
            // Si el usuario fue encontrado en la tabla 'usuariosc' (Usuarios de mesas)
            header("Location: ../php/solicitud.php"); // Redirige a la página de usuariosc
        }
        exit();
    } elseif (empty($results)) {
        $message = 'Lo sentimos, no hemos encontrado el usuario ingresado en nuestra base de datos.';
    } else {
        $message = 'Lo sentimos, esas credenciales no coinciden.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Regístrate</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }

    .container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 70vh;
    }

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
      width: 100%; /* botón de enviar ocupe todo el ancho */
      margin-top: 10px; /* Agrega algo de espacio encima del botón de enviar */
    }
  </style>
</head>
<body>
  <?php if (!empty($message)): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $message; ?>
    </div>
  <?php endif; ?>

  <div class="container">
    <h5 class="display-6 ms-4">Iniciar sesión</h5>
    <span><a href="registrar.php" class="btn btn-info custom-btn-color">ó Registrate</a></span>

    <form action="inicioSesion.php" method="POST">
      <input name="usuario" type="text" placeholder="Ingresa tu usuario">
      <input name="password" type="password" placeholder="Ingrese su contraseña">
      <input type="submit" value="Entrar" class="btn btn-primary custom-btn-color">
    </form>
  </div>
</body>
</html>

<?php include '../css/footer.php' ?>
