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

// Obtener eventos
$eventos = $leer_eventos->leerEventos();
if (!is_array($eventos)) {
    $eventos = [];
}

// Agrupar eventos por fecha
$eventosPorFecha = [];
foreach ($eventos as $evento) {
    $fecha = $evento['fecha'];
    $eventosPorFecha[$fecha][] = $evento;
}

// Manejar inscripción
$mensaje = "";
if (isset($_POST['inscribir'])) {
    $idE = filter_input(INPUT_POST, 'idE', FILTER_VALIDATE_INT);
    $idR = filter_input(INPUT_POST, 'idR', FILTER_VALIDATE_INT);
    $idSecciones = isset($_POST['idSeccion']) ? $_POST['idSeccion'] : [];

    $inscribir = false;

    if ($idE && $idR) {
        if (!empty($idSecciones)) {
            foreach ($idSecciones as $idS) {
                $inscribir = $leer_eventos->inscribirUsuario($idE, $idR, $idS);
            }
        } else {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/todo.css">
    <style>
        body {
            background-color: #f4f8fc;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .iconoT1 { width: 20px; height: 20px; object-fit: contain; }

        .search-bar {
            text-align: center;
            margin: 20px auto;
        }

        .search-bar input {
            width: 80%;
            max-width: 400px;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .search-bar input:focus {
            border-color: #4A7FA7;
            box-shadow: 0 0 5px rgba(74,127,167,0.5);
        }

        .btn-inscribir {
            background-color: #4A7FA7;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            width: 100%;
        }

        .btn-inscribir:hover {
            background-color: #1b7f4d;
        }

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
            background: #1b7f4d;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-secciones button:hover {
            background-color: #145C44;
        }

        .titulo-fecha {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1D2A62;
            text-align: center;
            position: relative;
            margin: 40px 0 20px;
        }

        .titulo-fecha::before,
        .titulo-fecha::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 80px;
            height: 2px;
            background-color: #87AECE;
        }

        .titulo-fecha::before { left: calc(50% - 140px); }
        .titulo-fecha::after { right: calc(50% - 140px); }

        .eventos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            padding: 0 40px 40px;
            max-width: 1300px;
            margin: auto;
        }

        .evento-card {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .evento-card:hover {
            transform: translateY(-4px);
            box-shadow: 0px 8px 18px rgba(0,0,0,0.15);
        }

        .evento-card h3 {
            color: #1D2A62;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .evento-card p {
            color: #333;
            font-size: 0.95rem;
            margin: 6px 0;
        }

        .mensaje {
            text-align: center;
            margin: 10px auto;
            padding: 10px;
            background-color: #e0f7fa;
            color: #006064;
            max-width: 600px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .titulo-fecha::before, .titulo-fecha::after {
                width: 50px;
            }
        }
        .form-secciones {
    display: none; /* Oculto por defecto */
}
        .tooltip-flotante {
    position: absolute;
    z-index: 9999;
    display: none; /* Oculto por defecto */
}

.tooltip-contenido {
    background-color: #1D2A62;
    color: #fff;
    padding: 12px 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    font-size: 0.95rem;
    position: relative;
    max-width: 250px;
}

.tooltip-texto {
    display: inline-block;
}

.tooltip-cerrar {
    position: absolute;
    top: 4px;
    right: 8px;
    cursor: pointer;
    font-weight: bold;
}


    </style>
</head>
<body>

<div class="nav">
    <div class="container">
        <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" class="iconoT1"></a></div>
        <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" class="iconoT1"></a></div>
        <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" class="iconoT1"></a></div>
    </div>
</div>

<h2 style="text-align:center;">Eventos Disponibles</h2>

<div class="search-bar">
    <input type="text" id="buscarEvento" placeholder="Buscar evento...">
</div>

<?php if (!empty($mensaje)): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<?php foreach ($eventosPorFecha as $fecha => $eventosDelDia): ?>
    <h3 class="titulo-fecha"><?= date('d/m/Y', strtotime($fecha)); ?></h3>
    <div class="eventos-grid">
        <?php foreach ($eventosDelDia as $evento): ?>
            <div class="evento-card">
                <h3><?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Hora:</strong> <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Lugar:</strong> <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>

                <button class="btn-inscribir" type="button" data-evento="<?= $evento['idE']; ?>">Inscribirme</button>

                <form method="POST" class="form-secciones">
                    <input type="hidden" name="idE" value="<?= (int)$evento['idE']; ?>">
                    <input type="hidden" name="idR" value="<?= (int)$_SESSION['idUsuario']; ?>">
                    <?php
                    $secciones = $seccionesModel->obtenerSeccionesPorEvento($evento['idE']);
                    if (!empty($secciones)):
                        foreach ($secciones as $sec): ?>
                            <label>
                                <input type="checkbox" name="idSeccion[]" value="<?= $sec['idSeccion']; ?>">
                                <?= htmlspecialchars($sec['nombre_seccion']); ?> - <?= htmlspecialchars($sec['hora_inicio']); ?>
                            </label>
                        <?php endforeach;
                    else: ?>
                        <p>Evento general (sin sub-secciones).</p>
                    <?php endif; ?>
                    <button type="submit" name="inscribir">Confirmar inscripción</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<script>
document.querySelectorAll('.btn-inscribir').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = btn.nextElementSibling;
        form.style.display = form.style.display === 'block' ? 'none' : 'block';
    });
});

function validarSeccion(form) {
    const checkboxes = form.querySelectorAll('input[name="idSeccion[]"]');
    if (checkboxes.length > 0 && !Array.from(checkboxes).some(cb => cb.checked)) {
        alert("Selecciona al menos una sección.");
        return false;
    }
    return true;
}

document.getElementById('buscarEvento')?.addEventListener('input', function () {
    const valor = this.value.toLowerCase();
    document.querySelectorAll('.evento-card').forEach(card => {
        const titulo = card.querySelector('h3').textContent.toLowerCase();
        card.style.display = titulo.includes(valor) ? '' : 'none';
    });
});
</script>
<script>
function mostrarTooltip() {
    const perfilBtn = document.querySelector('.btn a[href="perfil_usuario.php"]');
    const tooltip = document.getElementById('tooltipQR');

    if (!perfilBtn || !tooltip) return;

    const rect = perfilBtn.getBoundingClientRect();
    tooltip.style.top = (window.scrollY + rect.bottom + 10) + 'px';
    tooltip.style.left = (window.scrollX + rect.left) + 'px';
    tooltip.style.display = 'block';
}

function cerrarTooltip() {
    const tooltip = document.getElementById('tooltipQR');
    tooltip.style.display = 'none';
}

// Mostrar el tooltip después de un pequeño retardo (ej: 3 segundos)
window.addEventListener('load', () => {
    setTimeout(mostrarTooltip, 3000);
});
</script>

<div class="tooltip-flotante" id="tooltipQR">
    <div class="tooltip-contenido">
        <span class="tooltip-texto">Descarga tu QR al terminar tu inscripción a los eventos</span>
        <span class="tooltip-cerrar" onclick="cerrarTooltip()">×</span>
    </div>
</div>

</body>
</html>
