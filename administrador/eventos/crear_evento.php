<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/eventos.php";
require_once "../../assets/sentenciasSQL/secciones.php";

$mensaje = "";

if (isset($_POST['crear'])) {
    $idEvento      = random_int(10000000, 99999999);
    // Guardar texto crudo en la base de datos (UTF-8). Escaparemos al mostrar.
    $nombre_evento = trim($_POST['nombre_evento']);
    $descripcion   = trim($_POST['descripcion']);
    $fecha         = $_POST['fecha'];
    $hora          = $_POST['hora'];
    $lugar         = trim($_POST['lugar']);
    $aforo_max     = intval($_POST['aforo_max']);
    $mapa          = !empty($_POST['mapa']) ? trim($_POST['mapa']) : null;

    $crear_evento_obj = new Eventos();
    $crear_secciones  = new Secciones();

    // Crear evento
    $crear = $crear_evento_obj->crearEvento($idEvento, $nombre_evento, $descripcion, $fecha, $hora, $lugar, $aforo_max, $mapa);

    if ($crear === true) {
        // Insertar sub-eventos si existen
        if (!empty($_POST['seccion_nombre'])) {
            foreach ($_POST['seccion_nombre'] as $i => $nombreSeccion) {
                // Guardar el nombre crudo; se escapará al mostrarlo
                $nombreSeccion = trim($nombreSeccion);
                $horaInicio = $_POST['hora_inicio'][$i];
                $crear_secciones->crear_seccion($idEvento, $nombreSeccion, $horaInicio);
            }
        }

        echo "<script>alert('✅ Evento y sub-eventos creados correctamente'); window.location='eventos_admin.php';</script>";
        exit();
    } elseif ($crear === 'duplicado') {
        $mensaje = "⚠️ Un evento con la misma información ya existe.";
    } else {
        $mensaje = "❌ Error al crear el evento.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Evento</title>
    <script src="../../assets/js/validacion_evento.js"></script>
    <link rel="stylesheet" href="../../assets/css/eventos.css">
    <style>
        input.valid, textarea.valid { border: 2px solid green; background: #e8f5e9; }
        input.invalid, textarea.invalid { border: 2px solid red; background: #ffebee; }
        .seccion-item { margin-bottom: 10px; transition: all 0.3s ease; }
        .seccion-item.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>
    <header>
        <h2>Crear Evento</h2>
        <button class="regresar" onclick="window.history.back()"><span>Volver</span></button>
    </header>

    <form id="formEvento" action="" method="POST">
        <?php if(!empty($mensaje)) echo "<p style='color:red;text-align:center;'>$mensaje</p>"; ?>
        <label>Nombre del evento:</label>
        <input type="text" id="nombre_evento" name="nombre_evento" required>

        <label>Descripción:</label>
        <textarea id="descripcion" name="descripcion"></textarea>

        <label>Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>

        <label>Hora:</label>
        <input type="time" id="hora" name="hora" required>

        <label>Lugar:</label>
        <input type="text" id="lugar" name="lugar" required>

        <label>Capacidad máxima:</label>
        <input type="number" id="aforo_max" name="aforo_max" min="1" required>

        <label>Mapa (iframe o URL):</label>
        <textarea id="mapa" name="mapa" placeholder="Pega aquí el iframe o enlace del mapa"></textarea>

        <label>Sub-eventos:</label>
        <div id="contenedor-secciones"></div>
        <button type="button" id="agg_sub" onclick="agregarSeccion()">➕ Agregar un sub-evento</button>

        <br><br>
        <button type="submit" name="crear">Crear evento</button>
    </form>

    <script>
    // Sub-eventos dinámicos
    function agregarSeccion() {
        const div = document.createElement('div');
        div.classList.add('seccion-item');
        div.innerHTML = `
            <label>Nombre:</label>
            <input type="text" name="seccion_nombre[]" required>
            <label>Hora inicio:</label>
            <input type="time" name="hora_inicio[]" required>
            <button type="button" onclick="eliminarSeccion(this)">Eliminar</button>
        `;
        document.getElementById('contenedor-secciones').appendChild(div);
        setTimeout(() => div.classList.add('visible'), 10);
    }

    function eliminarSeccion(btn) {
        btn.parentElement.remove();
    }

    // Fecha mínima
    window.onload = function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('fecha').setAttribute('min', today);
    };
    </script>
</body>
</html>
