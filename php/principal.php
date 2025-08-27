<?php
session_name('camaras');
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

// Verificar si el rol es el adecuado (TI)
if ($_SESSION['rol'] !== 'TI') {
    header("Location: ../inicioSesion.php");
    exit();
}

// Conexión a la base de datos
require("connect.php");

// Obtener la fecha de hoy en formato Y-m-d
$fecha_hoy = date('Y-m-d');

// Consulta para obtener el número de solicitudes de hoy (con notificaciones no vistas)
$sql = "SELECT COUNT(*) FROM solicitud WHERE fecha >= :fecha_hoy AND visto = 0";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':fecha_hoy', $fecha_hoy);
$stmt->execute();
$numero_notificaciones = $stmt->fetchColumn(); // Obtiene el número de solicitudes de hoy

/// Obtener todas las notificaciones ordenadas por "visto" (1 primero) y luego por la fecha ascendente
$sql_notificaciones = "SELECT id, descripcion, cedis, fecha, hora_inicio, hora_fin, correo, fecha_hoy, notificacion, visto 
FROM solicitud 
ORDER BY visto ASC, fecha_hoy ASC";
$stmt_notificaciones = $conn->prepare($sql_notificaciones);
$stmt_notificaciones->execute();
$notificaciones = $stmt_notificaciones->fetchAll();

// Contar cuántas notificaciones tienen "visto" = 0
$sql_no_vistos = "SELECT COUNT(*) FROM solicitud WHERE visto = 0";
$stmt_no_vistos = $conn->prepare($sql_no_vistos);
$stmt_no_vistos->execute();
$numero_no_vistos = $stmt_no_vistos->fetchColumn();  // Obtiene el número de notificaciones no vistas


// Verificar si llegan datos desde notificaciones.php
$id_solicitud = isset($_GET['id']) ? $_GET['id'] : '';
$usuario_solicitante = isset($_GET['usuario']) ? $_GET['usuario'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Sistemas Serva</title>
    <style>
        .modal-backdrop {
            z-index: 0 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .drop-zone {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            color: #007bff;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .drop-zone.dragover {
            background-color: #e9ecef;
        }

        #progress-bar-container {
            display: none;
            margin-top: 20px;
        }

        progress {
            width: 100%;
            height: 20px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .file-item button {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-item button:hover {
            background-color: #c82333;
        }

        .no-visto {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
        }

        .visto {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
        }

        /* Estilo para el scroll en el modal */
        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .font-size-lg {
            font-size: 40px;
            /* Cambia el tamaño de fuente name user cerrar sesion */
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark fixed-top" style="background-color: #081856!important;">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="principal.php">
                    <img src="../img/loguito2.png" alt="" height="45" class="d-inline-block align-text-top">
                </a>
                <div class="welcome-text text-white d-none d-md-block">
                    Bienvenido de nuevo <?php echo $_SESSION['usuario']; ?>
                </div>

                <button type="button" class="position-relative border-0 bg-transparent p-0" data-bs-toggle="modal"
                    data-bs-target="#notificationsModal" aria-label="Ver notificaciones">
                    <i class="bi bi-bell fs-2 text-white"></i>
                    <?php if ($numero_no_vistos > 0) { ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $numero_no_vistos; ?>
                            <span class="visually-hidden">Notificaciones no vistas</span>
                        </span>
                    <?php } ?>
                </button>

                <!-- Modal de Notificaciones -->
                <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog"><br><br>
                        <div class="modal-content text-black" style="background-color: #081856!important;">
                            <div class="modal-header">
                                <h5 class="modal-title fs-5 text-white" id="notificationsModalLabel">Notificaciones
                                    Recientes</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    style="background-color: white" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Aquí van las notificaciones -->
                                <?php foreach ($notificaciones as $notificacion) { ?>
                                    <div class="notification-item <?php echo $notificacion['visto'] == 1 ? 'visto' : 'no-visto'; ?>"
                                        id="notificacion-<?php echo $notificacion['id']; ?>">
                                        <p><strong>Descripción:</strong> <?php echo $notificacion['descripcion']; ?></p>
                                        <p><strong>CEDIS:</strong> <?php echo $notificacion['cedis']; ?></p>
                                        <p><strong>Fecha:</strong> <?php echo $notificacion['fecha_hoy']; ?></p>
                                        <a href="marcarVisto.php?id=<?php echo $notificacion['id']; ?>"><i
                                                class="fa fa-eye fa-2x"></i></a>

                                        <!-- Botón para eliminar -->
                                        <button class="btn btn-danger btn-sm delete-notification"
                                            data-id="<?php echo $notificacion['id']; ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <hr>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <i class="fas fa-user-circle fa-2x"></i>
                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel" style="background-color: #081856!important;">
                    <div class="offcanvas-header">
                        <span class="text-white font-size-lg"><?php echo $_SESSION['usuario']; ?></span>
                        <button type="button" class="btn-close btn-lg" style="background-color: white"
                            data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="cerrarSesion.php">Cerrar Sesión</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div style="padding-top: 30px;"></div>

    <div class="container mt-5">
        <h1 class="display-5 text-center">Subir Video aclaración</h1><br>

        <form id="uploadForm" enctype="multipart/form-data">

            <!-- Campo para ID de la solicitud -->
            <div class="form-group mb-3">
                <label for="Id_solicitud" class="form-label">Id:</label>
                <input type="text" name="Id_solicitud" class="form-control" placeholder="Id de Solicitud"
                    id="Id_solicitud" value="<?php echo $id_solicitud; ?>" readonly />
            </div>

            <!-- Campo para Usuario -->
            <div class="form-group mb-3">
                <label for="usuario" class="form-label">Usuario:</label>
                <input type="text" name="usuario" class="form-control" placeholder="Usuario" id="usuario"
                    value="<?php echo htmlspecialchars($usuario_solicitante); ?>" readonly />
            </div>

            <div class="form-group">
                <label for="description">Descripción:</label>
                <textarea name="description" class="form-control" placeholder="Descripción del video"></textarea>
            </div><br>

            <!-- El campo de entrada de archivos ya tiene el atributo multiple -->
            <div id="drop-zone" class="drop-zone">
                <label for="media">Seleccione archivos multimedia:</label>
                <p>Arrastre sus archivos aquí o haga clic para seleccionar</p>
                <input type="file" id="media" name="media[]" class="form-control" accept=".mp4, .jpg, .jpeg, .png"
                    multiple required hidden>
            </div>
            <div id="file-list" class="file-list"></div><br>

            <button type="button" class="btn btn-primary" id="submitButton">Subir</button>
        </form>

        <div id="progress-bar-container">
            <p><strong>Progreso de carga:</strong></p>
            <progress id="progress-bar" value="0" max="100"></progress>
        </div>

        <a href="ver.php" class="btn btn-secondary mt-3">Ver Videos subidos</a>
    </div><br><br><br>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropZone = document.getElementById("drop-zone");
            const fileInput = document.getElementById("media");
            const fileList = document.getElementById("file-list");

            dropZone.addEventListener("click", function () {
                fileInput.click();
            });

            dropZone.addEventListener("dragover", function (e) {
                e.preventDefault();
                dropZone.classList.add("dragover");
            });

            dropZone.addEventListener("dragleave", function () {
                dropZone.classList.remove("dragover");
            });

            dropZone.addEventListener("drop", function (e) {
                e.preventDefault();
                dropZone.classList.remove("dragover");

                const files = e.dataTransfer.files;
                fileInput.files = files;

                mostrarListaArchivos(files);
            });

            fileInput.addEventListener("change", function () {
                mostrarListaArchivos(fileInput.files);
            });

            function mostrarListaArchivos(files) {
                fileList.innerHTML = "";
                Array.from(files).forEach((file, index) => {
                    let fileItem = document.createElement("div");
                    fileItem.classList.add("file-item");
                    fileItem.innerHTML = `
                <span>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                <button onclick="eliminarArchivo(${index})">X</button>
            `;
                    fileList.appendChild(fileItem);
                });
            }

            window.eliminarArchivo = function (index) {
                let dt = new DataTransfer();
                let files = fileInput.files;
                Array.from(files).forEach((file, i) => {
                    if (i !== index) dt.items.add(file);
                });
                fileInput.files = dt.files;
                mostrarListaArchivos(dt.files);
            };

            document.getElementById('submitButton').addEventListener('click', function () {
                var form = document.getElementById('uploadForm');
                var formData = new FormData(form);
                var progressBarContainer = document.getElementById('progress-bar-container');
                var progressBar = document.getElementById('progress-bar');

                progressBarContainer.style.display = 'block';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'subir.php', true);

                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var percent = (e.loaded / e.total) * 100;
                        progressBar.value = percent;
                    }
                });

                xhr.onload = function () {
                    if (xhr.status == 200) {
                        alert('Archivos subidos con éxito');
                        progressBar.value = 100;
                    } else {
                        alert('Error al subir los archivos.');
                    }
                };

                xhr.send(formData);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".delete-notification").forEach(button => {
                button.addEventListener("click", function () {
                    const id = this.getAttribute("data-id");
                    if (confirm("¿Estás seguro de que quieres eliminar esta notificación?")) {
                        fetch("eliminar_notificacion.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "id=" + id
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById("notificacion-" + id).remove();
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(error => console.error("Error:", error));
                    }
                });
            });
        });
    </script>
</body>

<?php include '../css/footer.php'; ?>

</html>