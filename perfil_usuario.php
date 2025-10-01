<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location:index.php");
    exit();
}

require_once __DIR__ . "/assets/lib/phpqrcode/qrlib.php";
require_once __DIR__ . "/assets/sentenciasSQL/usuarios.php"; 
require_once __DIR__ . "/assets/sentenciasSQL/eventos.php";  

$idUsuario = $_SESSION['idUsuario'];
$usuariosObj = new Usuarios();
$usuario = $usuariosObj->buscarUsuarioPorId($idUsuario); 

// Generar QR del usuario
ob_start();
QRcode::png($idUsuario, null, QR_ECLEVEL_Q, 5, 2);
$imageString = ob_get_clean();
$qrBase64 = base64_encode($imageString);
$qrDataUri = "data:image/png;base64," . $qrBase64;

// Leer eventos del usuario
$eventosObj = new Eventos();
$eventosInscritos = $eventosObj->leerEventosUsuario($idUsuario);
// Eliminar vento si se solicita
if(isset($_POST['eliminar']) && isset($_POST['idE'])) {
    $idE = intval($_POST['idE']);
    $idR = $_SESSION['idUsuario'];
    echo("<script>console.log('PHP: Eliminar evento ID: $idE para usuario ID: $idR');</script>");
    $eliminado = $usuariosObj->eliminarEvento($idR, $idE);
    if ($eliminado) {
        // Refrescar la lista de eventos
        $eventosInscritos = $eventosObj->leerEventosUsuario($idUsuario);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="./assets/css/todo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>
<body>
    <div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles</a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores</a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil</a></div>

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
    <header>
        <header>
    <h1 style="text-align:center;">Bienvenido, <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <div class="botones-usuario">
        <a href="modificar_usuario.php">
            <button>Modificar mis datos <div class="imagenes_botones"><img class="imagenes_acciones"src="assets/img/editar.png" alt="Icon"></div></button>
        </a>
        <form action="assets/php/cerrar_sesion.php" method="post">
            <button type="submit" class="btn-cerrar">Cerrar Sesión <div class="imagenes_botones"><img class="imagenes_acciones"src="assets/img/cierre-de-sesion-de-usuario.png" alt="Icon"></button>
        </form>
    </div>
</header>
    <br>
    
<div class="MUsuario">
    <p>Este es tu código QR para registrar tu asistencia a todos los eventos que asistas, ¡consérvalo!</p>
    <img id="qr" src="<?= $qrDataUri ?>" alt="Tu QR" width="200"><br><br>

    <!-- Botón para descargar el QR -->
    <a href="<?= $qrDataUri ?>" download="QR_Usuario_<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>.png">
        <button type="button">Descargar QR<div class="imagenes_botones"><img class="imagenes_acciones"src="assets/img/descargar.png" alt="Icon"></button>
    </a>
</div>

<br>
    <h2>Eventos a los que te has registrado:</h2>
    <div class="EventosUS"> 
        <?php if(!empty($eventosInscritos)): ?>
        <table class="tabla-eventos">
            <tr>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Lugar</th>
                <th></th>
            </tr>

            <?php foreach ($eventosInscritos as $evento): ?>
                <tr>
    <td> <?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td>  <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td>  <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td>  <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><form action="perfil_usuario.php" method="post" style="display:inline;">
        <input type="hidden" name="idE" value="<?= htmlspecialchars($evento['idE'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" name="eliminar" class="btn-accion"
            onclick="return confirm('¿Estás seguro de que deseas eliminar la asistencia a este evento?');">
            Anular asistencia
            <div class="imagenes_botones"><img class="imagenes_acciones"src="assets/img/borrar-usuario.png" alt="Icon">
        </button>
    </form></td>
            </tr>

            <?php endforeach; ?>
            </table>
    <?php else: ?>
        <p>No te has inscrito a ningún evento aún.</p>
    <?php endif; ?>
</div>
   <br><br>
    

    <script>
        if (window.history && history.pushState) {
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                window.location.replace('index.php');
            };
        }
    </script>
</body>
</html>
