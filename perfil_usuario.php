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
require_once __DIR__ . "/assets/php/helpers.php";

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

// Ordenar y agrupar por fecha y hora: primero ordenamos el array por fecha y hora (hora_inicio si existe, si no hora)
if (!empty($eventosInscritos) && is_array($eventosInscritos)) {
    usort($eventosInscritos, function($a, $b) {
        $dateA = strtotime($a['fecha']);
        $dateB = strtotime($b['fecha']);
        if ($dateA < $dateB) return -1;
        if ($dateA > $dateB) return 1;

        // misma fecha -> comparar hora
        $timeA = '';
        $timeB = '';
        if (!empty($a['hora_inicio'])) {
            $timeA = $a['hora_inicio'];
        } elseif (!empty($a['hora'])) {
            $timeA = $a['hora'];
        }
        if (!empty($b['hora_inicio'])) {
            $timeB = $b['hora_inicio'];
        } elseif (!empty($b['hora'])) {
            $timeB = $b['hora'];
        }

        // Normalizar formato de hora (si está vacío tratar como 00:00)
        if (empty($timeA)) $timeA = '00:00';
        if (empty($timeB)) $timeB = '00:00';

        $tA = strtotime($a['fecha'] . ' ' . $timeA);
        $tB = strtotime($b['fecha'] . ' ' . $timeB);
        if ($tA < $tB) return -1;
        if ($tA > $tB) return 1;
        return 0;
    });

    // Agrupar por fecha (YYYY-MM-DD)
    $eventosPorFecha = [];
    foreach ($eventosInscritos as $e) {
        $key = $e['fecha'];
        if (!isset($eventosPorFecha[$key])) $eventosPorFecha[$key] = [];
        $eventosPorFecha[$key][] = $e;
    }

    // Separar próximos y pasados
    $hoy = date('Y-m-d');
    $eventosProximosPorFecha = [];
    $eventosPasadosPorFecha = [];
    foreach ($eventosPorFecha as $fechaKey => $lista) {
        if ($fechaKey < $hoy) {
            $eventosPasadosPorFecha[$fechaKey] = $lista;
        } else {
            $eventosProximosPorFecha[$fechaKey] = $lista;
        }
    }
    if (!empty($eventosProximosPorFecha)) ksort($eventosProximosPorFecha);
    if (!empty($eventosPasadosPorFecha)) krsort($eventosPasadosPorFecha);
} else {
    $eventosPorFecha = [];
}

/* =====================
   ELIMINAR EVENTO
===================== */
if (isset($_POST['eliminar']) && isset($_POST['idE'])) {
    $idE = intval($_POST['idE']);

    /* 🔹 SOLO CAMBIO: usar sesión USER */
    $idR = $_SESSION['USER']['id'];

    echo("<script>console.log('PHP: Eliminar evento ID: $idE para usuario ID: $idR');</script>");
    
    // Si se envió idSeccion, eliminar solo esa inscripción; si no, eliminar todas las inscripciones al evento
    $idSeccion = isset($_POST['idSeccion']) && $_POST['idSeccion'] !== '' ? intval($_POST['idSeccion']) : null;
    $eliminado = $usuariosObj->eliminarEvento($idR, $idE, $idSeccion);
    if ($eliminado) {
        $eventosInscritos = $eventosObj->leerEventosUsuario($idUsuario);

        // Volver a ordenar y reagrupar después de la eliminación
            if (!empty($eventosInscritos) && is_array($eventosInscritos)) {
            usort($eventosInscritos, function($a, $b) {
                $dateA = strtotime($a['fecha']);
                $dateB = strtotime($b['fecha']);
                if ($dateA < $dateB) return -1;
                if ($dateA > $dateB) return 1;
                $timeA = !empty($a['hora_inicio']) ? $a['hora_inicio'] : ($a['hora'] ?? '00:00');
                $timeB = !empty($b['hora_inicio']) ? $b['hora_inicio'] : ($b['hora'] ?? '00:00');
                if (empty($timeA)) $timeA = '00:00';
                if (empty($timeB)) $timeB = '00:00';
                $tA = strtotime($a['fecha'] . ' ' . $timeA);
                $tB = strtotime($b['fecha'] . ' ' . $timeB);
                if ($tA < $tB) return -1;
                if ($tA > $tB) return 1;
                return 0;
            });

            $eventosPorFecha = [];
            foreach ($eventosInscritos as $e) {
                $key = $e['fecha'];
                if (!isset($eventosPorFecha[$key])) $eventosPorFecha[$key] = [];
                $eventosPorFecha[$key][] = $e;
            }

            // Re-separar próximos y pasados tras la eliminación
            $hoy = date('Y-m-d');
            $eventosProximosPorFecha = [];
            $eventosPasadosPorFecha = [];
            foreach ($eventosPorFecha as $fechaKey => $lista) {
                if ($fechaKey < $hoy) {
                    $eventosPasadosPorFecha[$fechaKey] = $lista;
                } else {
                    $eventosProximosPorFecha[$fechaKey] = $lista;
                }
            }
            if (!empty($eventosProximosPorFecha)) ksort($eventosProximosPorFecha);
            if (!empty($eventosPasadosPorFecha)) krsort($eventosPasadosPorFecha);
        } else {
            $eventosPorFecha = [];
        }
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
    Bienvenido, <?= safe_out($usuario['nombre']); ?>
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

    <a href="<?= $qrDataUri ?>" download="QR_Usuario_<?= safe_out($usuario['nombre']); ?>.png">
        <button type="button">Descargar QR</button>
    </a>
</div>

<br>

<h2>Eventos a los que te has registrado:</h2>

<div class="EventosUS">
    <?php if (!empty($eventosProximosPorFecha)): ?>
        <h2 style="text-align:center;">Próximos eventos</h2>
        <?php foreach ($eventosProximosPorFecha as $fecha => $eventosDia): ?>
            <h3 class="titulo-fecha" style="margin-top:18px;"><?= date('d/m/Y', strtotime($fecha)); ?></h3>
            <table class="tabla-eventos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Hora</th>
                        <th>Lugar</th>
                        <th>Sección</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($eventosDia as $evento): ?>
                    <tr>
                        <td><?= safe_out($evento['nombre']); ?></td>
                        <td>
                            <?php if (!empty($evento['hora_inicio'])): ?>
                                <?= safe_out($evento['hora_inicio']); ?> -
                            <?php else: ?>
                                <?= safe_out($evento['hora']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?= safe_out($evento['lugar']); ?></td>
                        <td><?= safe_out($evento['seccion'] ?: 'General'); ?></td>
                        <td>
                            <form action="perfil_usuario.php" method="post">
                                <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                                <?php if (!empty($evento['idSeccion'])): ?>
                                    <input type="hidden" name="idSeccion" value="<?= $evento['idSeccion']; ?>">
                                <?php endif; ?>
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
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">No tienes próximos eventos.</p>
    <?php endif; ?>

    <?php if (!empty($eventosPasadosPorFecha)): ?>
        <h2 style="text-align:center; margin-top:30px;">Eventos pasados</h2>
        <?php foreach ($eventosPasadosPorFecha as $fecha => $eventosDia): ?>
            <h3 class="titulo-fecha" style="margin-top:18px;"><?= date('d/m/Y', strtotime($fecha)); ?></h3>
            <table class="tabla-eventos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Hora</th>
                        <th>Lugar</th>
                        <th>Sección</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($eventosDia as $evento): ?>
                    <tr>
                        <td><?= safe_out($evento['nombre']); ?></td>
                        <td><?= !empty($evento['hora_inicio']) ? safe_out($evento['hora_inicio']) : safe_out($evento['hora']); ?></td>
                        <td><?= safe_out($evento['lugar']); ?></td>
                        <td><?= safe_out($evento['seccion'] ?: 'General'); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
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
