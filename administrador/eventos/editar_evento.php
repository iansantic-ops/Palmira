<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/conexion.php";
require_once "../../assets/sentenciasSQL/eventos.php";
require_once "../../assets/sentenciasSQL/secciones.php";

$eventosObj = new Eventos();
$seccionesObj = new Secciones();
$mensaje = "";

// Validar ID del evento
if (!isset($_GET['idE']) || !ctype_digit($_GET['idE'])) {
    header("Location: eventos_admin.php");
    exit();
}

$idEvento = intval($_GET['idE']);

// Obtener datos del evento
$evento = $eventosObj->leerEventoPorId($idEvento);
if (!$evento) {
    $mensaje = "⚠️ Evento no encontrado.";
}

// 🔹 Actualizar evento
if (isset($_POST['actualizar'])) {
    $nombre_evento = htmlspecialchars(trim($_POST['nombre_evento']), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $lugar = htmlspecialchars(trim($_POST['lugar']), ENT_QUOTES, 'UTF-8');
    $aforo_max = intval($_POST['aforo_max']);
    $mapa = !empty($_POST['mapa']) ? trim($_POST['mapa']) : null;

    $actualizado = $eventosObj->actualizarEvento($idEvento, $nombre_evento, $descripcion, $fecha, $hora, $lugar, $aforo_max, $mapa);

    if ($actualizado) {
        $mensaje = "✅ Evento actualizado correctamente.";
    } else {
        $mensaje = "❌ Error al actualizar el evento.";
    }
}

// 🔹 Guardar/actualizar secciones
if (isset($_POST['guardar_secciones'])) {
    if (isset($_POST['seccion_nombre'])) {
        foreach ($_POST['seccion_nombre'] as $i => $nombreSeccion) {
            $nombre = trim($nombreSeccion);
            $inicio = $_POST['hora_inicio'][$i];
            $idSeccion = $_POST['idSeccion'][$i] ?? '';

            if ($idSeccion) {
                // Actualizar existente
                $seccionesObj->actualizarSeccion($idSeccion, $nombre, $inicio);
            } else {
                // Crear nueva
                $seccionesObj->crear_seccion($idEvento, $nombre, $inicio);
            }
        }
        $mensaje = "✅ Secciones guardadas correctamente.";
    }
}

// 🔹 Eliminar sección
if (isset($_POST['eliminar_seccion'])) {
    $idSeccion = intval($_POST['idSeccion']);
    $seccionesObj->eliminarSeccion($idSeccion);
    $mensaje = "🗑️ Sección eliminada correctamente.";
}

// 🔹 Leer secciones del evento
$secciones = $seccionesObj->obtenerSeccionesPorEvento($idEvento);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Evento</title>
<link rel="stylesheet" href="../../assets/css/eventos.css">
<style>
input.valid, textarea.valid { border: 2px solid green; background: #e8f5e9; }
input.invalid, textarea.invalid { border: 2px solid red; background: #ffebee; }
.seccion-card {
    background: #fff;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
button {
    margin-top: 10px;
}
</style>
</head>
<body>
<header>
    <h2>Editar Evento</h2>
    <button class="regresar" onclick="window.history.back()">Volver</button>
</header>

<?php if ($mensaje): ?>
<p style="color:<?= str_contains($mensaje, '✅') ? 'green' : 'red' ?>;"><?= $mensaje ?></p>
<?php endif; ?>

<?php if ($evento): ?>
<form action="" method="POST">
    <label>Nombre del evento:</label>
    <input type="text" name="nombre_evento" value="<?= htmlspecialchars($evento['nombre'], ENT_QUOTES); ?>" required>

    <label>Descripción:</label>
    <textarea name="descripcion"><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES); ?></textarea>

    <label>Fecha:</label>
    <input type="date" name="fecha" value="<?= htmlspecialchars($evento['fecha'], ENT_QUOTES); ?>" required>

    <label>Hora:</label>
    <input type="time" name="hora" value="<?= htmlspecialchars($evento['hora'], ENT_QUOTES); ?>" required>

    <label>Lugar:</label>
    <input type="text" name="lugar" value="<?= htmlspecialchars($evento['lugar'], ENT_QUOTES); ?>" required>

    <label>Capacidad máxima:</label>
    <input type="number" name="aforo_max" min="1" value="<?= htmlspecialchars($evento['aforo_max'], ENT_QUOTES); ?>" required>

    <label>Mapa (iframe o URL):</label>
    <textarea name="mapa"><?= htmlspecialchars($evento['mapa'] ?? '', ENT_QUOTES); ?></textarea>

    <button type="submit" name="actualizar">💾 Actualizar evento</button>
</form>

<hr>

<h3> Secciones del evento</h3>

<form method="POST" id="formSecciones">
    <div id="contenedor-secciones">
        <?php foreach ($secciones as $sec): ?>
        <div class="seccion-card">
            <input type="hidden" name="idSeccion[]" value="<?= $sec['idSeccion']; ?>">
            <label>Nombre:</label>
            <input type="text" name="seccion_nombre[]" value="<?= htmlspecialchars($sec['nombre_seccion'], ENT_QUOTES); ?>" required>
            <label>Hora inicio:</label>
            <input type="time" name="hora_inicio[]" value="<?= htmlspecialchars($sec['hora_inicio'], ENT_QUOTES); ?>" required>
        
            <button type="submit" name="eliminar_seccion" value="1" onclick="return confirmarEliminar(this)">🗑️ Eliminar</button>
        </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="agregarSeccion()">➕ Agregar sección</button>
    <br><br>
    <button type="submit" name="guardar_secciones">💾 Guardar cambios en secciones</button>
</form>

<script>
function agregarSeccion() {
    const contenedor = document.getElementById('contenedor-secciones');
    const div = document.createElement('div');
    div.classList.add('seccion-card');
    div.innerHTML = `
        <input type="hidden" name="idSeccion[]" value="">
        <label>Nombre:</label>
        <input type="text" name="seccion_nombre[]" required>
        <label>Hora inicio:</label>
        <input type="time" name="hora_inicio[]" required>
        <button type="submit" name="eliminar_seccion" value="1"  onclick="this.parentElement.remove()">🗑️ Eliminar</button>
    `;
    contenedor.appendChild(div);
}

function confirmarEliminar(btn) {
    if (confirm("¿Eliminar esta sección permanentemente?")) {
        const form = document.getElementById('formSecciones');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'idSeccion';
        input.value = btn.parentElement.querySelector('[name="idSeccion[]"]').value;
        form.appendChild(input);
        return true;
    }
    return false;
}
</script>

<?php else: ?>
<p>Evento no encontrado.</p>
<?php endif; ?>

</body>
</html>
