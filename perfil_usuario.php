<?php
session_start();

/* =====================
   VALIDAR SESIÓN DE USUARIO
===================== */
if (!isset($_SESSION['USER'])) {
    header("Location:index.php");
    exit();
}

/* 🔹 SOLO CAMBIO: obtener ID desde USER */
$idUsuario = $_SESSION['USER']['id'];

require_once __DIR__ . "/assets/lib/phpqrcode/qrlib.php";
require_once __DIR__ . "/assets/sentenciasSQL/usuarios.php"; 
require_once __DIR__ . "/assets/sentenciasSQL/eventos.php";  

$usuariosObj = new Usuarios();
$usuario = $usuariosObj->buscarUsuarioPorId($idUsuario); 

/* =====================
   GENERAR QR
===================== */
// 🔹 Guardar configuración actual de errores
$old_error_reporting = error_reporting();

// 🔹 Desactivar warnings y notices solo temporalmente
error_reporting(E_ERROR | E_PARSE);

ob_start();
QRcode::png($idUsuario, null, QR_ECLEVEL_Q, 5, 2);
$imageString = ob_get_clean();
$qrBase64 = base64_encode($imageString);
$qrDataUri = "data:image/png;base64," . $qrBase64;

// 🔹 Restaurar configuración original
error_reporting($old_error_reporting);


/* =====================
   LEER EVENTOS DEL USUARIO
===================== */
$eventosObj = new Eventos();
$eventosInscritos = $eventosObj->leerEventosUsuario($idUsuario);

/* =====================
   ELIMINAR EVENTO
===================== */
if (isset($_POST['eliminar']) && isset($_POST['idE'])) {
    $idE = intval($_POST['idE']);

    /* 🔹 SOLO CAMBIO: usar sesión USER */
    $idR = $_SESSION['USER']['id'];

    echo("<script>console.log('PHP: Eliminar evento ID: $idE para usuario ID: $idR');</script>");
    
    $eliminado = $usuariosObj->eliminarEvento($idR, $idE);
    if ($eliminado) {
        $eventosInscritos = $eventosObj->leerEventosUsuario($idUsuario);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <style>
    .iconoT1 {
      width: 20px;   
      height: 20px;  
      object-fit: contain;      
      image-rendering: crisp-edges; 
    }
    </style>

    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="./assets/css/todo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
</head>
<body>

<div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" class="iconoT1"></a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" class="iconoT1"></a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" class="iconoT1"></a></div>

    <svg class="outline" viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg">
      <rect class="rect" pathLength="100" x="0" y="0" width="400" height="60"
        fill="transparent" stroke-width="4"></rect>
    </svg>
  </div>
</div>

<br>

<header>
    <h1 style="text-align:center;">
        Bienvenido, <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>
    </h1>
    <div class="botones-usuario">
        <a href="modificar_usuario.php">
            <button>Modificar mis datos <img src="./assets/img/lapiz-de-usuario.png" class="iconoT1"></button>
        </a>
        <form action="logout_usuario.php" method="post">
            <button type="submit" class="btn-cerrar">
                Cerrar Sesión <img src="./assets/img/cierre-de-sesion-de-usuario.png" class="iconoT1">
            </button>
        </form>
    </div>
</header>

<br>

<div class="MUsuario">
    <p>Este es tu código QR para registrar tu asistencia a todos los eventos que asistas, ¡consérvalo!</p>
    <img id="qr" src="<?= $qrDataUri ?>" alt="Tu QR" width="200"><br><br>

    <a href="<?= $qrDataUri ?>" download="QR_Usuario_<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>.png">
        <button type="button">Descargar QR</button>
    </a>
</div>

<br>

<h2>Eventos a los que te has registrado:</h2>

<div class="EventosUS"> 
<?php if (!empty($eventosInscritos)): ?>
<table class="tabla-eventos">
<thead>
<tr>
<th>Nombre</th>
<th>Fecha</th>
<th>Hora</th>
<th>Lugar</th>
<th>Sección</th>
<th>Acción</th>
</tr>
</thead>
<tbody>
<?php foreach ($eventosInscritos as $evento): ?>
<tr>
<td><?= htmlspecialchars($evento['nombre']); ?></td>
<td><?= htmlspecialchars($evento['fecha']); ?></td>
<td>
<?php if (!empty($evento['hora_inicio'])): ?>
<?= htmlspecialchars($evento['hora_inicio']); ?> -
<?php else: ?>
<?= htmlspecialchars($evento['hora']); ?>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($evento['lugar']); ?></td>
<td><?= htmlspecialchars($evento['seccion'] ?: 'General'); ?></td>
<td>
<form action="perfil_usuario.php" method="post">
<input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
<button type="submit" name="eliminar"
onclick="return confirm('¿Estás seguro de que deseas eliminar la asistencia a este evento?');">
Anular asistencia <img src="./assets/img/borrar-usuario.png" class="iconoT1">
</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p>No te has inscrito a ningún evento aún.</p>
<?php endif; ?>
</div>

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
