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

$listaSecciones = $seccionesModel->obtenerSecciones();
$eventosFiltrados = [];
$mensaje = "";

// Verificar si se filtró por sección
$mostrarEventos = false;
if (isset($_GET['idSeccion'])) {
    $idSeccion = filter_input(INPUT_GET, 'idSeccion', FILTER_VALIDATE_INT);
    if ($idSeccion) {
        $eventosFiltrados = $leer_eventos->leerEventosPorSeccion($idSeccion);
        $mostrarEventos = true;
    }
}

// Manejar inscripción
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
    <style>
        .iconoT1 {
            width: 20px;
            height: 20px;
            object-fit: contain;
            image-rendering: crisp-edges;
        }

        .search-bar {
            margin: 20px auto;
            text-align: center;
        }

        .search-bar input[type="text"] {
            width: 80%;
            max-width: 400px;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .search-bar input[type="text"]:focus {
            border-color: #4A7FA7;
            box-shadow: 0 0 5px rgba(74, 127, 167, 0.5);
        }

        .search-bar button {
            padding: 10px 20px;
            margin-left: 10px;
            background-color: #4A7FA7;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-bar button:hover {
            background-color: #1b7f4d;
        }

        .secciones-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .seccion-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            width: 250px;
        }

        .seccion-card a {
            display: inline-block;
            margin-top: 10px;
            background-color: #4A7FA7;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }

        .seccion-card a:hover {
            background-color: #1b7f4d;
        }
    </style>
</head>
<body>

<div class="nav">
    <div class="container">
        <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" alt="Icono PNG" class="iconoT1"></a></div>
        <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" alt="Icono PNG" class="iconoT1"></a></div>
        <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" alt="Icono PNG" class="iconoT1"></a></div>
    </div>
</div>

<br>

<?php if (!$mostrarEventos): ?>
    <h2>Secciones Disponibles</h2>
    <div class="secciones-grid">
        <?php foreach($listaSecciones as $sec): ?>
            <div class="seccion-card">
                <h3><?= htmlspecialchars($sec['nombre_seccion'], ENT_QUOTES); ?></h3>
                <p><?= htmlspecialchars($sec['descripcion'], ENT_QUOTES); ?></p>
                <a href="eventos_disponibles.php?idSeccion=<?= $sec['idSeccion']; ?>">Ver eventos</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($mostrarEventos): ?>
    <h2>Eventos de la sección</h2>
<div style="text-align:center; margin-bottom: 20px;">
        <a href="eventos_disponibles.php" class="btn-accion">Volver a Secciones</a>
    </div>
    <div class="search-bar">
        <input type="text" id="buscarEvento" placeholder="Buscar evento...">
    </div>

<!-- BARRA DE BÚSQUEDA -->
<div class="search-bar">
    <input type="text" id="buscarEvento" placeholder="Buscar evento...">
</div>


<?php if (!empty($mensaje)): ?>
    <div class="mensaje">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<!-- Contenedor en grid -->
<div class="eventos-grid" id="gridEventos">
<?php foreach ($result as $row): ?>
    <div class="evento">
        <div class="evento-header">
            <h3><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
        </div>
        <div class="evento-body">
            <p><?= htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong><img src="assets/img/calendario.png" alt="Icono PNG" class="iconoT1"> Fecha:</strong> <?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong><img src="assets/img/reloj.png" alt="Icono PNG" class="iconoT1"> Hora:</strong> <?= htmlspecialchars($row['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong><img src="assets/img/marcador.png" alt="Icono PNG" class="iconoT1"> Lugar:</strong> <?= htmlspecialchars($row['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>

    <div class="eventos-grid" id="gridEventos">
        <?php foreach ($eventosFiltrados as $row): ?>
            <div class="evento">
                <div class="evento-header">
                    <h3><?= htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                </div>
                <div class="evento-body">
                    <p><?= htmlspecialchars($row['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><img src="assets/img/calendario.png" class="iconoT1"> Fecha:</strong> <?= htmlspecialchars($row['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><img src="assets/img/reloj.png" class="iconoT1"> Hora:</strong> <?= htmlspecialchars($row['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><img src="assets/img/marcador.png" class="iconoT1"> Lugar:</strong> <?= htmlspecialchars($row['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <form action="eventos_disponibles.php?idSeccion=<?= (int)$idSeccion ?>" method="POST">
                        <input type="hidden" name="idE" value="<?= (int)$row['idE']; ?>">
                        <input type="hidden" name="idR" value="<?= (int)$_SESSION['idUsuario']; ?>" required>
                        <button type="submit" name="inscribir">Inscribirme <img src="./assets/img/editar.png" alt="Icono PNG" class="iconoT1"></button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="apps">
    <div class="btn-principal">
        <img src="assets/img/mensajes.png"  class="iconoT1" alt="Redes">
    </div>
    <div class="redes">
        <a href="https://wa.me/+525551721475" id="whatsapp"><img src="assets/img/whatsapp.png" class="iconoT1" alt="Whatsapp"></a>
        <a href="https://www.facebook.com/Universidad.Palmira" id="facebook"><img src="assets/img/facebook.png" class="iconoT1" alt="facebook"></a>
        <a href="https://www.instagram.com/comunidad.palmira/?igshid=z4ifdpudkidw" id="instagram"><img src="assets/img/instagram.png" class="iconoT1" alt="instagram"></a>
    </div>
</div>

<script>
document.getElementById('buscarEvento')?.addEventListener('input', function() {
    const input = this.value.toLowerCase();
    const eventos = document.querySelectorAll('#gridEventos .evento');
    eventos.forEach(evento => {
        const nombre = evento.querySelector('h3').textContent.toLowerCase();
        evento.style.display = nombre.includes(input) ? '' : 'none';
    });
});

if (window.history && history.pushState) {
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        window.location.replace('login.php');
    };
}
</script>

</body>
</html>
