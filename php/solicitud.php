<?php
session_name('camaras'); // Asegúrate de tener el mismo nombre de sesión para el proyecto
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../inicioSesion.php");
    exit();
}

// Verificar si el rol es el adecuado (Operador)
if ($_SESSION['rol'] !== 'Operador') {
    // Si el rol no es "Operador", redirigir a la página de error o de inicio de sesión
    header("Location: ../inicioSesion.php");
    exit();
}


// Continuar con el resto del código si el rol es "Operador"
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <title>Evidencia Serva</title>
    <style>
        .welcome-text {
            flex: 1;
            text-align: center;
            display: none;
        }

        @media (min-width: 769px) {
            .welcome-text {
                display: block;
            }
        }

        .form-group.hidden {
            display: none;
        }

        .form-group .error-message {
            color: red;
            display: none;
        }

        /* Spinner de carga */
        #loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: 1000;
            width: 4rem;
            height: 4rem;
            border-width: 0.25rem;
        }

        body.loading #loading-spinner {
            display: block;
        }

        body.loading * {
            pointer-events: none;
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

        .file-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }

        .remove-file {
            background-color: #ff5c5c;
            border: none;
            color: white;
            padding: 3px 7px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .remove-file:hover {
            background-color: #ff2b2b;
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
                <a class="navbar-brand text-white" href="solicitud.php">
                    <img src="../img/loguito2.png" alt="" height="45" class="d-inline-block align-text-top">
                </a>

                <!-- Texto de bienvenida visible solo en pantallas medianas y grandes -->
                <div class="welcome-text text-white d-none d-md-block">
                    Bienvenido de nuevo <?php echo $_SESSION['usuario']; ?>
                </div>

                <!-- Botón de menú desplegable con el icono de usuario -->
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <i class="fas fa-user-circle fa-2x"></i> <!-- Ícono de usuario -->
                </button>

                <!-- Offcanvas para menú lateral -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel" style="background-color: #081856!important;">
                    <div class="offcanvas-header">
                        <!-- Nombre del usuario en la cabecera del menú lateral -->
                        <span class="text-white font-size-lg"><?php echo $_SESSION['usuario']; ?></span>
                        <button type="button" class="btn-close btn-lg" style="background-color: white"
                            data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav mr-auto">
                            <!-- Opción para visualizar los videos del usuario -->
                            <li class="nav-item">
                                <a class="btn btn-outline-info" href="video.php">Video Cámaras</a>
                            </li>
                        </ul>
                        <br><br>
                        <ul class="navbar-nav mr-auto">
                            <!-- Opción para cerrar sesión dentro del menú lateral -->
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
        <h1 class="display-6 text-center">Solicitar Evidencia de Camaras</h1><br>
        <form id="formulario" method="POST" action="../db/database_form_solicitud.php">

            <!-- <div class="form-group mb-3">
                <label for="motivo" class="form-label">Motivo: </label>
                <input type="text" name="motivo" class="form-control" id="motivo" aria-describedby="nameHelp"
                    placeholder="Ingrese el Motivo" />
            </div> -->

            <div class="form-group">
                <label for="description">Descripción:</label>
                <textarea name="description" class="form-control" placeholder="Descripcion de solicitud"
                    required></textarea>
            </div>

            <div class="form-group">
                <label for="cedis" class="form-label" name="cedis">Cedis: </label><br>
                <select class="form-control" id="cedis" name="cedis" required>
                    <option value="">Seleccione el Cedis…</option>
                    <option value="PACHUCA">Pachuca</option>
                    <option value="CANCUN">Cancun</option>
                    <option value="CHIHUAHUA">Chihuahua</option>
                    <option value="CULIACAN">Culiacan</option>
                    <option value="CUERNAVACA">Cuernavaca</option>
                    <option value="CORDOBA">Cordoba</option>
                    <option value="GUADALAJARA">Guadalajara</option>
                    <option value="HERMOSILLO">Hermosillo</option>
                    <option value="LEON">Leon</option>
                    <option value="MERIDA">Merida</option>
                    <option value="MONTERREY">Monterrey</option>
                    <option value="OAXACA">Oaxaca</option>
                    <option value="PUEBLA">Puebla</option>
                    <option value="QUERETARO">Queretaro</option>
                    <option value="SAN LUIS POTOSI">San Luis Potosi</option>
                    <option value="TUXTLA GUTIERREZ">Tuxtla Gutuerrez</option>
                    <option value="VERACRUZ">Veracruz</option>
                    <option value="VILLAHERMOSA">Villahermosa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha" class="form-label">Fecha y hora aproximada del video a solicitar:</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <!-- Campo de fecha -->
                    <input type="date" name="fecha" id="fecha" class="form-control" style="width: 150px;" required>

                    <!-- Texto 'Entre' -->
                    <span>Entre</span>

                    <!-- Campo de hora de inicio -->
                    <input type="time" name="hora_inicio" id="hora_inicio" class="form-control" style="width: 100px;"
                        required>

                    <!-- Texto 'a' -->
                    <span>a</span>

                    <!-- Campo de hora de fin -->
                    <input type="time" name="hora_fin" id="hora_fin" class="form-control" style="width: 100px;"
                        required>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="exampleInputEmail1" class="form-label">Enviar correo a: </label>
                <div id="correos-container">
                    <div class="input-group mb-2">
                        <input type="email" name="correos[]" class="form-control" aria-describedby="nameHelp"
                            placeholder="Ingrese un correo" required />
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary add-email-btn">
                                <i class="bi bi-plus-square-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-5">
                <button type="submit" class="btn btn-outline-primary">Solicitar</button>
            </div>
        </form>
    </div><br><br><br><br><br>

    <script>
        // Código JavaScript para añadir y eliminar correos
        document.addEventListener('click', function (event) {
            if (event.target.closest('.add-email-btn')) {
                const container = document.getElementById('correos-container');

                // Crear un nuevo input group
                const newInputGroup = document.createElement('div');
                newInputGroup.className = 'input-group mb-2';

                // Crear el nuevo campo de entrada
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.name = 'correos[]';
                newInput.className = 'form-control';
                newInput.placeholder = 'Ingrese otro correo';
                newInput.required = true;

                // Crear el botón para eliminar el campo
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'btn btn-outline-danger remove-email-btn';
                removeButton.innerHTML = '<i class="bi bi-x-square-fill"></i>';

                // Crear un div para el botón de eliminación
                const removeDiv = document.createElement('div');
                removeDiv.className = 'input-group-append';
                removeDiv.appendChild(removeButton);

                // Agregar el campo de entrada y el botón de eliminación al nuevo input group
                newInputGroup.appendChild(newInput);
                newInputGroup.appendChild(removeDiv);

                // Añadir el nuevo input group al contenedor
                container.appendChild(newInputGroup);
            }

            if (event.target.closest('.remove-email-btn')) {
                const emailField = event.target.closest('.input-group');
                emailField.remove();
            }
        });

        // Validación de correos
        document.getElementById('formulario').addEventListener('submit', function (e) {
            const emailInputs = document.querySelectorAll('input[name="correos[]"]');
            let valid = true;

            emailInputs.forEach(input => {
                if (input.value.trim() === '') {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Por favor, complete todos los campos de correo.');
            }
        });
    </script>

</body>

<?php include '../css/footer.php'; ?>

</html>