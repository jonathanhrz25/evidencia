<?php
session_name('camaras');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

// Obtener el nombre de usuario logueado
$usuario_logueado = $_SESSION['usuario'];

// Incluir la conexión
include('connect.php');

// Consulta para obtener los videos del usuario logueado
$query = "SELECT * FROM videos WHERE usuario = :usuario";

// Si usas PDO
if (isset($conn) && $conn instanceof PDO) {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuario', $usuario_logueado, PDO::PARAM_STR);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Si usas MySQLi
elseif (isset($conn) && $conn instanceof mysqli) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $usuario_logueado); // 's' es el tipo de dato (string)
    $stmt->execute();
    $result = $stmt->get_result();
    $videos = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Estilos de la página */
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .video-list {
            background-color: #081856;
            padding: 10px;
            height: 100vh;
            overflow-y: auto;
        }

        .video-list a {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f8f9fa;
            color: black;
            text-align: center;
            padding: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .video-list a img {
            width: 20%;
            height: auto;
            border-radius: 5px;
        }

        .video-list a:hover {
            background-color: #0cb7f2;
        }

        .video-list a span {
            margin-top: 10px;
            display: block;
            word-break: break-all;
        }

        .video-container {
            background-color: #23305e;
            color: white;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: left;
            transition: margin-left 0.3s;
            margin: 20px;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .sidebar-nav {
            position: fixed;
            top: 56px;
            left: -250px;
            width: 250px;
            height: 100%;
            background-color: #081856;
            transition: left 0.3s;
            z-index: 1050;
        }

        .sidebar-nav.show {
            left: 0;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s;
            padding: 20px;
        }

        .main-content.expanded {
            margin-left: 250px;
        }

        .navbar-toggler {
            margin-left: auto;
        }
    </style>
    <title>Sistemas Camaras</title>
</head>

<body style="padding-top: 70px;">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #081856!important;">
            <a class="navbar-brand" href="./solicitud.php">
                <img src="../img/loguito2.png" alt="" height="45" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler" type="button" id="toggleSidebar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>

        <!-- Sidebar de videos -->
        <nav class="sidebar-nav" id="sidebarNav">
            <div class="video-list">
                <h3 class="text-center text-white">Lista de Video Aclaraciones</h3>
                <?php
                if (isset($videos) && count($videos) > 0) {
                    foreach ($videos as $video) {
                        $video_path = 'uploads/' . $video['video']; // Ajusta la ruta de acuerdo a dónde almacenas los videos
                        $fileExtension = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));

                        if (in_array($fileExtension, ['mp4'])) {
                            echo '<a href="video.php?Id=' . urlencode($video['Id']) . '">';
                            echo '<img src="../img/play.png" alt="Imagen de muestra">';
                            echo '<span>' . htmlspecialchars($video['descripcion']) . '</span>';
                            echo '</a>';
                        }
                    }
                } else {
                    echo "<p class='text-white'>No se encontraron videos.</p>";
                }
                ?>
            </div>
        </nav>
    </header>

    <main class="main-content" id="mainContent">
        <div class="video-container">
            <?php
            if (isset($_GET['Id'])) {
                $id = $_GET['Id'];

                // Consulta para obtener el video y su descripción desde la base de datos
                $stmt = $conn->prepare("SELECT * FROM videos WHERE Id = :Id");
                $stmt->bindParam(':Id', $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $file = $row['video'];
                    $descripcion = $row['descripcion'];
                    $filePath = 'uploads/' . $file;

                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                    if (in_array($fileExtension, ['mp4'])) {
                        echo "<div class='video-wrapper'>";
                        echo "<video controls poster='../img/serva.jpg' id='video-element'>";
                        echo "<source src='" . htmlspecialchars($filePath) . "' type='video/mp4'>";
                        echo "</video>";
                        echo "</div>";
                    } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        echo "<div class='image-wrapper'>";
                        echo "<img src='" . htmlspecialchars($filePath) . "' alt='Imagen' class='img-fluid'>";
                        echo "</div>";
                    } else {
                        echo "<p>El archivo seleccionado no es compatible.</p>";
                    }

                    echo "<h4>Descripción: " . htmlspecialchars($descripcion) . "</h4>";
                } else {
                    echo "<p>El video no se encuentra disponible.</p>";
                }
            } else {
                echo "<p>Seleccione un archivo de la lista para visualizar.</p>";
            }
            ?>
        </div><br><br><br><br>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebarNav = document.getElementById('sidebarNav');
            var mainContent = document.getElementById('mainContent');
            var toggleSidebarButton = document.getElementById('toggleSidebar');

            function hideSidebar() {
                sidebarNav.classList.remove('show');
                mainContent.classList.remove('expanded');
                localStorage.setItem('sidebarState', 'closed');
            }

            toggleSidebarButton.addEventListener('click', function () {
                sidebarNav.classList.toggle('show');
                mainContent.classList.toggle('expanded');
                if (sidebarNav.classList.contains('show')) {
                    localStorage.setItem('sidebarState', 'open');
                } else {
                    localStorage.setItem('sidebarState', 'closed');
                }
            });

            if (localStorage.getItem('sidebarState') === 'open') {
                sidebarNav.classList.add('show');
                mainContent.classList.add('expanded');
            }

            var videoLinks = document.querySelectorAll('.video-list a');
            videoLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        hideSidebar();
                    }
                });
            });
        });
    </script>
</body>

</html>

<?php include '../css/footer.php'; ?>