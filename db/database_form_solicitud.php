<?php
session_name('camaras'); // Asegúrate de que sea el mismo nombre de sesión
session_start(); // Inicia la sesión para acceder a las variables de sesión

require("../php/connect.php"); // Conexión a la base de datos PDO
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar que el usuario está en sesión
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    echo "<script>alert('No se ha encontrado el usuario en sesión.'); window.location.href = '../php/solicitud.php';</script>";
    exit();
}

// Obtener el nombre de usuario de la sesión
$usuario_nombre = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $descripcion = $_POST['description'];
    $cedis = $_POST['cedis'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $correos = $_POST['correos']; // Array de correos

    // Generar la fecha de hoy (formato: YYYY-MM-DD)
    $fecha_hoy = date('Y-m-d');

    // Combina la fecha con la hora de inicio y fin
    $fecha_inicio = $fecha . ' ' . $hora_inicio;
    $fecha_fin = $fecha . ' ' . $hora_fin;

    // Convertir array de correos a string
    $correos_str = implode(',', array_map('trim', $correos));

    // Crear la notificación
    $notificacion = "Solicitud de $usuario_nombre: $descripcion, CEDIS: $cedis, Fecha: $fecha_hoy";

    // SQL para insertar los datos en la base de datos
    $sql = "INSERT INTO solicitud (descripcion, cedis, fecha, hora_inicio, hora_fin, correo, fecha_hoy, notificacion, visto, usuario_solicitante)
            VALUES (:descripcion, :cedis, :fecha, :hora_inicio, :hora_fin, :correos_str, :fecha_hoy, :notificacion, 0, :usuario_nombre)"; // 'visto' es 0 inicialmente (no visto)

    try {
        // Preparar la consulta PDO
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':cedis', $cedis);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $fecha_inicio); // Usamos la fecha combinada con la hora
        $stmt->bindParam(':hora_fin', $fecha_fin); // Usamos la fecha combinada con la hora
        $stmt->bindParam(':correos_str', $correos_str);
        $stmt->bindParam(':fecha_hoy', $fecha_hoy); // Fecha de hoy
        $stmt->bindParam(':notificacion', $notificacion); // Notificación
        $stmt->bindParam(':usuario_nombre', $usuario_nombre);

        // Ejecutar la consulta
        $insertar = $stmt->execute();

        if ($insertar) {
            // Enviar correos usando PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'mail.smtp2go.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ticket@serva.com.mx';
                $mail->Password = 'Serva123.*';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 2525;

                // Cambiar remitente a nombre del usuario logueado
                $usuario_nombre = $_SESSION['usuario'];
                $mail->setFrom('ticket@serva.com.mx', $usuario_nombre); // Usando el nombre de usuario de sesión

                // Mensaje de correo
                $body = "
                    <html>
                        <body>
                            <p>A quien corresponda,</p>
                            <p>Un gusto saludarle, le compartimos la siguiente solicitud de evidencia de cámaras:</p>
                            <p>Descripción: $descripcion</p>
                            <p>Fecha y hora: $fecha de $hora_inicio a $hora_fin</p>
                            <p>Notificación: $notificacion</p>
                            <p>Saludos cordiales,<br>$usuario_nombre</p>
                        </body>
                    </html>
                ";

                $mail->isHTML(true);
                $mail->Subject = 'Solicitud de Evidencia de Camaras';
                $mail->Body = $body;

                // Agregar los correos a los que se enviará el mensaje
                foreach ($correos as $correo) {
                    $mail->addAddress(trim($correo));
                }

                // Enviar el correo
                if ($mail->send()) {
                    echo "<script>alert('Correo(s) enviado(s) exitosamente'); window.location.href = '../php/solicitud.php';</script>";
                } else {
                    echo "<script>alert('Error al enviar el correo'); window.location.href = '../php/solicitud.php';</script>";
                }

            } catch (Exception $e) {
                echo "<script>alert('Hubo un error al enviar el correo: {$mail->ErrorInfo}'); window.location.href = '../php/solicitud.php';</script>";
            }

        } else {
            echo "<script>alert('ERROR: Sus datos no han sido registrados'); window.location.href = '../php/solicitud.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error en la consulta: " . $e->getMessage() . "'); window.location.href = '../php/solicitud.php';</script>";
    }
}
?>
