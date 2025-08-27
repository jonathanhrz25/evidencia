<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../img/icono2.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
  <style>
    .footer-content {
      display: none;
      transition: max-height 0.3s ease;
      background-color: rgb(8, 24, 86);
      padding: 1em;
    }

    .footer-content.show {
      display: block;
    }

    .toggle-button {
      cursor: pointer;
      font-size: 1.5em;
      color: white;
    }

    .footer-container {
      background-color: rgb(8, 24, 86);
      padding: 1em;
      position: relative;
    }

    .toggle-container {
      position: absolute;
      top: 10px;
      right: 20px;
    }

    .programmer-image {
      max-width: 100px;
      border-radius: 50%;
    }

    @media (min-width: 992px) {
      .footer-content .container {
        max-width: 90%;
      }

      .footer-content .row {
        align-items: center;
      }

      .footer-content .col-md-2,
      .footer-content .col-md-5 {
        text-align: center;
      }

      .footer-content .col-md-5 {
        text-align: left;
      }
    }
  </style>

  <footer class="fixed-bottom bg-blue bg-gradient text-center text-lg-start text-white">
    <div class="footer-container">
      <div class="text-center">
        © 2025 Copyright:
        <label class="text-white">Automotriz Serva - </label>
        <a class="text-white" href="https://serva.com.mx/" target="_blank">Pagina: Automotriz Serva</a>
      </div>
      <div class="toggle-container">
        <!-- <i class="fa fa-chevron-down toggle-button" onclick="toggleFooterContent()"
          aria-label="Expandir contenido del pie de página"></i> -->
      </div>
    </div>

    <!-- <div class="footer-content text-center">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-2 mb-3">
            <img src="../img/jonathan.jpg" alt="Programmer Image" class="programmer-image">
            <div>Programador:</div>
            <div>Ing. Jonathan Ismael Hernández Alarcón</div>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <h5>Lenguajes</h5><br>
            <p>APACHE, HTML5, PHP, MySQL, JS, CSS, etc.</p>
          </div>
          <div class="col-12 col-md-4 mb-3 text-start text-md-center">
            <h5>Proyecto</h5>
            <p>Estadísticas Cedis</p>
            <p>Consiste en la visualización del status de la infraestructura de TI en Cedis</p>
          </div>
        </div>
      </div>
    </div> -->
  </footer>

  <script>
    function toggleFooterContent() {
      const content = document.querySelector('.footer-content');
      content.classList.toggle('show');
      const toggleIcon = document.querySelector('.toggle-button');
      if (content.classList.contains('show')) {
        toggleIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
      } else {
        toggleIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
      }
    }
  </script>

  </body>

</html>