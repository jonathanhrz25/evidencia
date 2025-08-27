<?php include '../css/header.php' ?>

<?php
require 'connect.php';

// Inicializamos las variables de sesión
session_name('camaras');
session_start();

$message1 = '';
$message2 = '';

// Verificar si se envió el formulario de administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_submit'])) {
    $adminUsuario = $_POST['admin_usuario'];
    $adminPassword = $_POST['admin_password'];

    // Validar las credenciales del administrador en la tabla 'usuariosti' (rol TI)
    $sql = "SELECT id, usuario, password FROM usuariosti WHERE usuario = :usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario', $adminUsuario);
    $stmt->execute();
    $adminCredentials = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificamos que el administrador tenga el ID 1
    if ($adminCredentials && $adminCredentials['id'] == 1) {
        // Verificar las credenciales del administrador
        if (password_verify($adminPassword, $adminCredentials['password'])) {
            // Credenciales de administrador válidas, configurar variable de sesión
            $_SESSION['admin_verified'] = true;
            $message1 = 'Credenciales de administrador verificadas correctamente.';
        } else {
            $message2 = 'Credenciales de administrador incorrectas';
        }
    } else {
        $message2 = 'No se encontró el usuario administrador';
    }
}

// Verificar si el administrador ya ha sido verificado
if (isset($_SESSION['admin_verified']) && $_SESSION['admin_verified']) {
    // Verificar si se envió el formulario de registro de usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_submit'])) {
        if (!empty($_POST['usuario']) && !empty($_POST['password']) && !empty($_POST['rol']) && !empty($_POST['area'])) {
            // Verificar si las contraseñas coinciden
            if ($_POST['password'] === $_POST['confirm_password']) {

                // Dependiendo del rol, insertamos en la tabla correspondiente
                if ($_POST['rol'] == 'TI') {
                    // Insertar en la tabla 'usuariosti' para rol TI
                    $sql = "INSERT INTO usuariosti (usuario, password, rol, area) VALUES (:usuario, :password, 'TI', :area)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':usuario', $_POST['usuario']);
                    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $stmt->bindParam(':password', $pass);
                    $stmt->bindParam(':area', $_POST['area']);
                } elseif ($_POST['rol'] == 'Operador') {
                    // Insertar en la tabla 'usuariosc' para rol Operador
                    $sql = "INSERT INTO usuariosc (usuario, password, rol, area) VALUES (:usuario, :password, 'Operador', :area)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':usuario', $_POST['usuario']);
                    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
                    $stmt->bindParam(':password', $pass);
                    $stmt->bindParam(':area', $_POST['area']);
                }

                // Ejecutar la consulta de inserción
                if ($stmt->execute()) {
                    $message1 = 'Nuevo usuario creado correctamente';
                } else {
                    $message2 = 'Lo sentimos, debe haber habido un problema al crear tu cuenta';
                }
            } else {
                $message2 = 'Las contraseñas no coinciden.';
            }
        } else {
            $message2 = 'Por favor, complete todos los campos.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Regístrate</title>
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
            width: 100%;
            /* botón de enviar ocupe todo el ancho */
            margin-top: 10px;
            /* Agrega algo de espacio encima del botón de enviar */
        }
    </style>
</head>

<body>
    <div class="container"><br><br><br>
        <?php if (!empty($message1)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message1; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($message2)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $message2; ?>
            </div>
        <?php endif; ?>

        <h5 class="display-6 ms-4">Regístrate</h5>
        <span><a href="inicioSesion.php" class="btn btn-info custom-btn-color">ó Iniciar sesión</a></span>

        <?php if (!isset($_SESSION['admin_verified']) || !$_SESSION['admin_verified']): ?>
            <!-- Formulario de administrador -->
            <form action="registrar.php" method="POST">
                <input type="hidden" name="admin_submit" value="1">
                <div class="form-group">
                    <input name="admin_usuario" type="text" class="form-control" placeholder="Usuario de administrador"
                        required>
                </div>
                <div class="form-group">
                    <input name="admin_password" type="password" class="form-control"
                        placeholder="Contraseña de administrador" required>
                </div>
                <input type="submit" value="Verificar Administrador" class="btn btn-primary custom-btn-color">
            </form>
        <?php else: ?>
            <!-- Formulario de registro de usuario -->
            <form action="registrar.php" method="POST">
                <input type="hidden" name="user_submit" value="1">

                <!-- Input Usuario -->
                <div class="form-group">
                    <input name="usuario" type="text" class="form-control" placeholder="Ingresa tu Usuario" required>
                </div>

                <!-- Input Contraseña -->
                <div class="form-group">
                    <input name="password" type="password" class="form-control" placeholder="Ingrese su contraseña"
                        required>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="form-group">
                    <input name="confirm_password" type="password" class="form-control" placeholder="Confirmar contraseña"
                        required>
                </div>

                <!-- Selección de Área -->
                <div class="form-group">
                    <select class="form-control" id="area" name="area" required>
                        <option value="">Seleccione el área del Usuario</option>
                        <option value="ADQUISICIONES">Adquisiciones</option>
                        <option value="ADMINISTRACION CEDIS">Administracion Cedis</option>
                        <option value="ADMINISTRACION REFACCIONARIA">Administracion Refaccionaria</option>
                        <option value="ALMACEN">Almacen</option>
                        <option value="CENTRO DE ATENCION AL CLIENTES">Centro de Atención al Cliente</option>
                        <option value="BODEGAS">Bodegas</option>
                        <option value="CEDIS">Cedis</option>
                        <option value="COMPRAS">Compras</option>
                        <option value="CONTABILIDAD">Contabilidad</option>
                        <option value="CREDITO Y COBRANZA">Credito y Cobranza</option>
                        <option value="DEVOLUCIONES">Devoluciones</option>
                        <option value="EMBARQUES">Embarques</option>
                        <option value="FACTURACION">Facturacion</option>
                        <option value="FINANZAS">Finanzas</option>
                        <option value="FLOTILLAS">Flotillas</option>
                        <option value="IFUEL">IFuel</option>
                        <option value="INVENTARIOS">Inventarios</option>
                        <option value="JURIDICO">Juridico</option>
                        <option value="MERCADOTECNIA">Mercadotecnia</option>
                        <option value="MODELADO DE PRODUCTOS">Modelado de Productos</option>
                        <option value="PICKING">Picking</option>
                        <option value="PRECIOS ESPECIALES">Precios Especiales</option>
                        <option value="RECURSOS HUMANOS">Recursos Humanos</option>
                        <option value="RECEPCION">Recepcion</option>
                        <option value="RECEPCION DE MATERIALES">Recepcion de Materiales</option>
                        <option value="REABASTOS">Reabastos</option>
                        <option value="SERVICIO MEDICO">Servicio Medico</option>
                        <option value="SISTEMAS">Sistemas</option>
                        <option value="SURTIDO CEDIS">Surtido Cedis</option>
                        <option value="TELEMARKETING">Telemarketing</option>
                        <option value="VIGILANCIA">Vigilancia</option>
                        <option value="VENTAS">Ventas</option>
                    </select>
                </div>

                <!-- Selección de Rol -->
                <div class="form-group">
                    <select name="rol" class="form-control" id="rol" required>
                        <option value="">Seleccione el Rol del Usuario</option>
                        <option value="TI">TI</option>
                        <option value="Operador">Operador</option>
                    </select>
                </div>

                <input type="submit" value="Registrar Usuario" class="btn btn-primary custom-btn-color">
            </form>
        <?php endif; ?>
    </div>

</body>

</html>

<?php include '../css/footer.php' ?>