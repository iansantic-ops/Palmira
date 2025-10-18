<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/conexion.php";
require_once "../../assets/sentenciasSQL/eventos.php";

$eventosObj = new Eventos();
$mensaje = "";

// Validar ID del evento
if (!isset($_GET['idE']) || !ctype_digit($_GET['idE'])) {
    header("Location: eventos.php");
    exit();
}

$idEvento = $_GET['idE'];

// Obtener datos actuales
$evento = $eventosObj->leerEventoPorId($idEvento);
if (!$evento) {
    $mensaje = " Evento no encontrado.";
}

// Procesar actualización
if (isset($_POST['actualizar'])) {
    $nombre_evento  = htmlspecialchars(trim($_POST['nombre_evento']), ENT_QUOTES, 'UTF-8');
    $descripcion    = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $fecha          = $_POST['fecha'];
    $hora           = $_POST['hora'];
    $lugar          = htmlspecialchars(trim($_POST['lugar']), ENT_QUOTES, 'UTF-8');
    $aforo_max      = intval($_POST['aforo_max']);
    $mapa           = !empty($_POST['mapa']) ? trim($_POST['mapa']) : null;

    $actualizado = $eventosObj->actualizarEvento($idEvento, $nombre_evento, $descripcion, $fecha, $hora, $lugar, $aforo_max, $mapa);

    if ($actualizado === true) {
        echo "<script>alert('Evento editado exitosamente'); window.location='eventos_admin.php';</script>";
        exit();
    } else {
        $mensaje = "Error al actualizar el evento. Intenta de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Evento</title>
<link rel="stylesheet" href="../../assets/css/eventos.css">
<script src="../../assets/js/validacion_evento.js"></script>
<style>
input.valid, textarea.valid { border: 2px solid green; background: #e8f5e9; }
input.invalid, textarea.invalid { border: 2px solid red; background: #ffebee; }
</style>
</head>
<body>
<header>
    <h2>Editar Evento</h2>
    <button class="regresar" onclick="window.history.back()">   
        <span>Volver</span>
    </button>
</header>

<?php if ($mensaje): ?>
    <p style="color:<?= strpos($mensaje,'')!==false ? 'green' : 'red' ?>;"><?= $mensaje ?></p>
<?php endif; ?>

<?php if ($evento): ?>
<form id="formEvento" action="" method="POST">
    <label>Nombre del evento:</label>
    <input type="text" id="nombre_evento" name="nombre_evento" 
        value="<?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>

    <label>Descripción:</label>
    <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>

    <label>Fecha:</label>
    <input type="date" id="fecha" name="fecha" 
        value="<?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?>" required>

    <label>Hora:</label>
    <input type="time" id="hora" name="hora" 
        value="<?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?>" required>

    <label>Lugar:</label>
    <input type="text" id="lugar" name="lugar" 
        value="<?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?>" required>

    <label>Capacidad máxima:</label>
    <input type="number" id="aforo_max" name="aforo_max" min="1" 
        value="<?= htmlspecialchars($evento['aforo_max'], ENT_QUOTES, 'UTF-8'); ?>" required>

    <label>Mapa (iframe o URL):</label>
    <textarea id="mapa" name="mapa" placeholder="Pega aquí el iframe o URL del mapa"><?= htmlspecialchars($evento['mapa'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

    <br><br>
    <button type="submit" name="actualizar">Actualizar evento</button>
</form>
<?php else: ?>
    <p>Evento no encontrado.</p>
<?php endif; ?>

</body>
</html>
