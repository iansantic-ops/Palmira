<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location:index.php");
    exit();
}

include_once("assets/sentenciasSQL/eventos.php");
$leer_eventos = new Eventos();
$result = $leer_eventos->leerEventos();
$mensaje = "";

if (isset($_POST['inscribir'])) {
    $idE = filter_input(INPUT_POST, 'idE', FILTER_VALIDATE_INT);
    $idR = filter_input(INPUT_POST, 'idR', FILTER_VALIDATE_INT);

    if ($idE && $idR) {
        $inscribir = $leer_eventos->inscribirUsuario($idE, $idR);
        if ($inscribir === 'true') {
            $mensaje = "Inscripción realizada correctamente.";
        } elseif ($inscribir === 'duplicado') {
            $mensaje = "Ya estás inscrito en este evento.";
        } else {
            $mensaje = "Error al inscribirse. Por favor, intenta de nuevo.";
        }
    } else {
        $mensaje = "Datos inválidos. Verifique su información.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eventos Disponibles</title>
    <link rel="stylesheet" href="./assets/css/todo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>
<style>
    .iconoT1 {
      width: 20px;   
      height: 20px;  
      object-fit: contain;      
      image-rendering: crisp-edges; 
    }
  </style>
<body>

<div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" alt="Icono PNG" class="iconoT1"></a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" alt="Icono PNG" class="iconoT1"></a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" alt="Icono PNG" class="iconoT1"></a></div>

    <svg
      class="outline"
      viewBox="0 0 400 60"
      xmlns="http://www.w3.org/2000/svg"
      preserveAspectRatio="xMidYMid meet"
    >
      <rect
        class="rect"
        pathLength="100"
        x="0"
        y="0"
        width="400"
        height="60"
        fill="transparent"
        stroke-width="4"
      ></rect>
    </svg>
  </div>
</div>
<br>
    <h2>Lista de Eventos</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Contenedor en grid -->
    <div class="eventos-grid">
    <?php foreach ($result as $row): ?>
        <div class="evento">
            <div class="evento-header">
                <h3><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
            </div>
            <div class="evento-body">
                <p><?= htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Fecha:</strong> <?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Hora:</strong> <?= htmlspecialchars($row['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Lugar:</strong> <?= htmlspecialchars($row['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>

                <form action="eventos_disponibles.php" method="POST">
                    <input type="hidden" name="idE" value="<?= (int)$row['idE']; ?>">
                    <input type="hidden" name="idR" value="<?= (int)$_SESSION['idUsuario']; ?>" required>
                    <button type="submit" name="inscribir">Inscribirme <img src="./assets/img/editar.png" alt="Icono PNG" class="iconoT1"></button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="apps">
  <div class="btn-principal">
    <img src="assets/img/mensajes.png"  class="iconoT1" alt="Redes">
  </div>
  <div class="redes">
    <a href="https://wa.me/+525551721475" id="whatsapp"><img src="assets/img/whatsapp.png"  class="iconoT1" alt="Whatsapp"></a>
    <a href="https://www.facebook.com/Universidad.Palmira" id="facebook"><img src="assets/img/facebook.png"  class="iconoT1" alt="facebook"></a>
    <a href="https://www.instagram.com/comunidad.palmira/?igshid=z4ifdpudkidw" id="instagram"><img src="assets/img/instagram.png" class="iconoT1" alt="instagram"></a>
  </div>
</div>


    <script>
    if (window.history && history.pushState) {
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            window.location.replace('login.php');
        };
    }
    </script>
</body>
</html>