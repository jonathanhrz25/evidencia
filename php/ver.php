<?php
session_name('camaras');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

require 'connect.php'; // Conexión a la base de datos
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
        html,
        body {
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

        @media (max-width: 768px) {
            .video-container {
                padding: -5px;
                margin: -10px;
            }
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

        .image-wrapper img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
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

        @media (min-width: 768px) {
            .sidebar-nav {
                top: 70px;
            }
        }

        /* Botón de alternar el sidebar visible siempre */
        #toggleSidebar {
            display: block !important;
        }

        /* Ocultar el botón de navegación solo en pantallas grandes */
        @media (min-width: 768px) {
            .navbar-toggler[data-target="#navbarNav"] {
                display: none;
                /* Oculta el botón del menú de navegación en pantallas grandes */
            }
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
            <a class="navbar-brand" href="./principal.php">
                <img src="../img/loguito2.png" alt="" height="45" class="d-inline-block align-text-top">
            </a>
            <!-- Este botón solo aparece en pantallas pequeñas -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Este botón se muestra siempre (para el sidebar) -->
            <button class="navbar-toggler" type="button" id="toggleSidebar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <span class="nav-link text-white">
                            Visualización de Videos
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <form action="" method="GET" class="form-inline ml-auto">
                        <input class="form-control mr-sm-2" type="search" name="busqueda"
                            placeholder="Buscar por Fecha o No. Cliente" aria-label="Search" style="width: 270px;">
                        <button class="btn btn-primary my-2 my-sm-0" name="enviar" type="submit">Buscar</button>
                    </form>
                </ul>
            </div>
        </nav>

        <!-- Sidebar de videos -->
        <nav class="sidebar-nav" id="sidebarNav">
            <div class="video-list">
                <h3 class="text-center text-white">Lista de Video Aclaraciones</h3>
                <?php
                // Consulta para obtener los videos desde la base de datos
                if (isset($_GET['enviar'])) {
                    $busqueda = $_GET['busqueda'];
                    $stmt = $conn->prepare("SELECT * FROM videos WHERE descripcion LIKE :busqueda");
                    $searchTerm = "%" . $busqueda . "%";
                    $stmt->bindParam(':busqueda', $searchTerm);
                    $stmt->execute();
                } else {
                    $stmt = $conn->prepare("SELECT * FROM videos");
                    $stmt->execute();
                }

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<a href='ver.php?Id=" . urlencode($row['Id']) . "'>";
                        echo "<img src='../img/play.png' alt='Imagen de muestra'>";
                        echo "<span>" . htmlspecialchars($row['descripcion']) . "</span>";
                        echo "</a>";
                    }
                } else {
                    echo "<p class='text-white'>No hay videos disponibles.</p>";
                }
                ?><br><br><br><br>
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

                    // Definir extensiones de videos permitidos
                    $allowedVideoExtensions = ['mp4', 'avi', 'mov', 'webm', 'flv', 'mkv'];

                    // Verificar si es un formato de video
                    if (in_array($fileExtension, $allowedVideoExtensions)) {
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

            // Función para alternar el sidebar
            toggleSidebarButton.addEventListener('click', function () {
                sidebarNav.classList.toggle('show');
                mainContent.classList.toggle('expanded');
                // Guardar estado
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