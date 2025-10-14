<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/eventos.php";
require_once "../../assets/sentenciasSQL/secciones.php";

if (isset($_POST['crear'])) {
    $idEvento      = random_int(10000000, 99999999);
    $nombre_evento = htmlspecialchars(trim($_POST['nombre_evento']), ENT_QUOTES, 'UTF-8');
    $descripcion   = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');
    $fecha         = $_POST['fecha'];
    $hora          = $_POST['hora'];
    $lugar         = htmlspecialchars(trim($_POST['lugar']), ENT_QUOTES, 'UTF-8');
    $aforo_max     = intval($_POST['aforo_max']);

    $crear_evento = new Eventos();
    $crear_secciones = new Secciones();

    // Primero crear el evento
    $crear = $crear_evento->crearEvento($idEvento, $nombre_evento, $descripcion, $fecha, $hora, $lugar, $aforo_max);

    if ($crear === true) {
        // Si el evento se creó correctamente, insertar las secciones
        if (!empty($_POST['seccion_nombre'])) {
            foreach ($_POST['seccion_nombre'] as $i => $nombreSeccion) {
                $nombreSeccion = htmlspecialchars(trim($nombreSeccion), ENT_QUOTES, 'UTF-8');
                $horaInicio = $_POST['hora_inicio'][$i];
                $crear_secciones->crear_seccion($idEvento, $nombreSeccion, $horaInicio);
            }
        }

        echo "<script>alert('✅ Evento y sub-eventos creados correctamente'); window.location='eventos_admin.php';</script>";
        exit();
    } elseif ($crear === 'duplicado') {
        echo "<script>alert('⚠️ Un evento con la misma información ya existe.');</script>";
    } else {
        echo "<script>alert('❌ Error al crear el evento.');</script>";
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

</head>
<style>
input.valid, textarea.valid {
  border: 2px solid green;
  background: #e8f5e9;
}

input.invalid, textarea.invalid {
  border: 2px solid red;
  background: #ffebee;
}
</style>
<body>
    <header>
        <h2>Crear Evento</h2>
        <button class="regresar" onclick="window.history.back()">   
            <span>Volver</span>
        </button>
    </header>
     
    
   <form id="formEvento" action="crear_evento.php" method="POST">
    <label>Nombre del evento:</label>
    <input type="text" id="nombre_evento" name="nombre_evento" required>

    <label>Descripción:</label>
    <textarea id="descripcion" name="descripcion"></textarea>

    <label for="date">Fecha:</label>
    <input type="date" id="fecha" name="fecha" required>

    <label>Hora:</label>
    <input type="time" id="hora" name="hora" required>

    <label>Lugar:</label>
    <input type="text" id="lugar" name="lugar" required>

    <label>Capacidad máxima:</label>
    <input type="number" id="aforo_max" name="aforo_max" min="1" required>


<label>Sub-eventos</label>
<div id="contenedor-secciones">

</div>

<button type="button" id="agg_sub"onclick="agregarSeccion()">➕ Agregar un "Sub-evento"</button>

<script>
// ...existing code...
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
  // Espera un momento y agrega la clase visible para activar la transición
  setTimeout(() => div.classList.add('visible'), 10);
}
// ...existing code...

function eliminarSeccion(btn) {
  btn.parentElement.remove();
}
</script>

    <br><br>
    <button type="submit" name="crear">Crear evento</button>
</form>


    <br>
    

    <script>
        window.onload = function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').setAttribute('min', today);
        };
    </script>
</body>
</html>
