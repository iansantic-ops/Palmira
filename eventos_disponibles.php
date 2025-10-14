<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location:index.php");
    exit();
}

include_once("assets/sentenciasSQL/eventos.php");
include_once("assets/sentenciasSQL/secciones.php");

$leer_eventos = new Eventos();
$seccionesModel = new Secciones();

// ✅ Asegura que leerEventos devuelva un array
$eventos = $leer_eventos->leerEventos();
if (!is_array($eventos)) {
    $eventos = [];
}

$mensaje = "";

// 🔹 Manejar inscripción
if (isset($_POST['inscribir'])) {
    $idE = filter_input(INPUT_POST, 'idE', FILTER_VALIDATE_INT);
    $idR = filter_input(INPUT_POST, 'idR', FILTER_VALIDATE_INT);
    $idSecciones = isset($_POST['idSeccion']) ? $_POST['idSeccion'] : [];

    // ✅ Evita el warning
    $inscribir = false;

    if ($idE && $idR) {
        if (!empty($idSecciones)) {
            foreach ($idSecciones as $idS) {
                $inscribir = $leer_eventos->inscribirUsuario($idE, $idR, $idS);
            }
        } else {
            // Evento general sin sub-secciones
            $inscribir = $leer_eventos->inscribirUsuario($idE, $idR, null);
        }

        if ($inscribir === 'true') {
            $mensaje = "✅ Inscripción realizada correctamente.";
        } elseif ($inscribir === 'duplicado') {
            $mensaje = "⚠️ Ya estás inscrito en este evento.";
        } else {
            $mensaje = "❌ Error al inscribirse. Por favor, intenta de nuevo.";
        }
    } else {
        $mensaje = "⚠️ Datos inválidos. Verifica tu información.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eventos Disponibles</title>
    <link rel="stylesheet" href="assets/css/todo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
.iconoT1 { width: 20px; height: 20px; object-fit: contain; }

.search-bar { text-align: center; margin: 20px auto; }
.search-bar input { width: 80%; max-width: 400px; padding: 10px 15px; border-radius: 8px; border: 1px solid #ccc; }
.search-bar input:focus { border-color: #4A7FA7; box-shadow: 0 0 5px rgba(74,127,167,0.5); }

.btn-inscribir {
  background-color: #4A7FA7; color: #fff; border: none; padding: 10px 15px;
  border-radius: 8px; cursor: pointer; transition: 0.3s; margin-top: 10px;
}
.btn-inscribir:hover { background-color: #1b7f4d; }

.form-secciones {
  margin-top: 10px;
  background: #f9f9f9;
  border-radius: 10px;
  padding: 10px 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.form-secciones label {
  display: block;
  margin: 5px 0;
}
.form-secciones button {
  background: #1b7f4d; color: white; border: none; padding: 8px 12px; border-radius: 6px;
  cursor: pointer; transition: 0.3s;
}
.form-secciones button:hover { background-color: #145C44; }
</style>
<body>

<div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" alt="" class="iconoT1"></a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" alt="" class="iconoT1"></a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" alt="" class="iconoT1"></a></div>
  </div>
</div>

<h2>Eventos Disponibles</h2>

<div class="search-bar">
  <input type="text" id="buscarEvento" placeholder="Buscar evento...">
</div>

<?php if (!empty($mensaje)): ?>
  <div class="mensaje"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="eventos-grid" id="gridEventos">
<?php foreach ($eventos as $row): ?>
  <div class="evento">
    <div class="evento-header">
      <h3><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
    </div>
    <div class="evento-body">
      <p><?= htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
      <p><strong><img src="assets/img/calendario.png" class="iconoT1"> Fecha:</strong> <?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8'); ?></p> 
      <p><strong><img src="assets/img/reloj.png" class="iconoT1"> Hora:</strong> <?= htmlspecialchars($row['hora'], ENT_QUOTES, 'UTF-8'); ?></p> 
      <p><strong><img src="assets/img/marcador.png" class="iconoT1"> Lugar:</strong> <?= htmlspecialchars($row['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>

      <button type="button" class="btn-inscribir" data-evento="<?= (int)$row['idE']; ?>">Inscribirme</button>

      <!-- Contenedor oculto para secciones -->
      <form method="POST" class="form-secciones" style="display:none;" onsubmit="return validarSeccion(this)">
          <input type="hidden" name="idE" value="<?= (int)$row['idE']; ?>">
          <input type="hidden" name="idR" value="<?= (int)$_SESSION['idUsuario']; ?>">

          <div class="secciones-container" id="secciones-<?= (int)$row['idE']; ?>">
              <?php
              $seccionesEvento = $seccionesModel->obtenerSeccionesPorEvento($row['idE']);
              if (!empty($seccionesEvento)):
                  foreach ($seccionesEvento as $sec): ?>
                      <label>
                          <input type="checkbox" name="idSeccion[]" 
                                 value="<?= $sec['idSeccion']; ?>">
                          <?= htmlspecialchars($sec['nombre_seccion']); ?> 
                          - <?= htmlspecialchars($sec['hora_inicio']);?>
                      </label>
                  <?php endforeach;
              else: ?>
                  <p>Evento general (sin sub-secciones).</p>
              <?php endif; ?>
          </div>

          <button type="submit" name="inscribir">Confirmar inscripción</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.btn-inscribir').forEach(btn => {
  btn.addEventListener('click', () => {
    const form = btn.nextElementSibling;
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  });
});

// Validar selección (si hay secciones)
function validarSeccion(form) {
  const checkboxes = form.querySelectorAll('input[name="idSeccion[]"]');
  if (checkboxes.length > 0) {
    let seleccionado = false;
    checkboxes.forEach(c => { if (c.checked) seleccionado = true; });
    if (!seleccionado) {
      alert("Por favor selecciona al menos una sección.");
      return false;
    }
  }
  return true;
}

// Filtro de búsqueda
document.getElementById('buscarEvento')?.addEventListener('input', function () {
  const input = this.value.toLowerCase();
  const eventos = document.querySelectorAll('#gridEventos .evento');
  eventos.forEach(evento => {
      const nombre = evento.querySelector('h3').textContent.toLowerCase();
      evento.style.display = nombre.includes(input) ? '' : 'none';
  });
});
</script>
</body>
</html>
