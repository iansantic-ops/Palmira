<?php
session_start();
require_once __DIR__ . "../../../assets/sentenciasSQL/eventos.php";
require_once __DIR__ . "../../../assets/sentenciasSQL/secciones.php";

// Verificar sesión del admin
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

$eventos = new Eventos();
$secciones = new Secciones();

// ✅ Eliminar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminarEvento'])) {
    $idEliminar = intval($_POST['idE']);
    if ($eventos->eliminarEvento($idEliminar)) {
        echo "<script>alert('Evento eliminado correctamente'); window.location='eventos_admin.php?idSeccion={$_GET['idSeccion']}';</script>";
    } else {
        echo "<script>alert('Error al eliminar el evento');</script>";
    }
}

// ✅ Eliminar sección (y sus eventos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminarSeccion'])) {
    $idSeccion = intval($_POST['idSeccion']);
    if ($secciones->eliminarSeccionYEventos($idSeccion)) {
        echo "<script>alert('Sección y sus eventos eliminados correctamente'); window.location='eventos_admin.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la sección');</script>";
    }
}

// Mostrar secciones o eventos
$idSeccion = isset($_GET['idSeccion']) ? intval($_GET['idSeccion']) : 0;

if ($idSeccion > 0) {
    $listaEventos = $eventos->leerEventosPorSeccion($idSeccion);
} else {
    $listaSecciones = $secciones->obtenerSecciones();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background: #0A1931;
            color: white;
            padding: 15px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            gap: 10px;
        }

        header h1 {
            flex: 1 1 100%;
            margin: 0;
            font-size: 1.5rem;
        }

        header a button {
            background-color: #4A7FA7;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: 0.3s;
        }

        header a button:hover {
            background-color: #1b7f4d;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }

        .headerCardEventos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .headerCardEventos button {
            background: none;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .headerCardEventos button:hover {
            transform: scale(1.1);
        }

        .icono-AM {
            width: 24px;
            height: 24px;
        }

        .btn {
            display: inline-block;
            background-color: #4A7FA7;
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.3s;
            margin-top: 15px;
        }

        .btn:hover {
            background-color: #1b7f4d;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
            }

            .container {
                padding: 10px;
                gap: 15px;
            }

            .card {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<header>
    <a href="../menu_admin.php"><button>Volver al menú</button></a>
    <h1><?= $idSeccion > 0 ? 'Eventos por Sección' : 'Secciones Disponibles'; ?></h1>
    <div class="acciones-header">
        <a href="../perfil_admin.php"><button>Perfil</button></a>
        <a href="crear_seccion.php"><button>Nueva Sección</button></a>
        <a href="crear_evento.php"><button><img src="../../assets/img/subir evento.png" alt="Crear" class="icono-AM"></button></a>
    </div>
</header>

<div class="container">

<?php if ($idSeccion == 0): ?>
    <!-- 🔹 SECCIONES -->
    <?php if (!empty($listaSecciones)): ?>
        <?php foreach ($listaSecciones as $seccion): ?>
            <div class="card">
                <div class="headerCardEventos">
                    <a href="editar_seccion.php?idSeccion=<?= $seccion['idSeccion']; ?>">
                        <button><img src="../../assets/img/lapiz.png" class="icono-AM" alt="Editar"></button>
                    </a>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta sección y todos sus eventos?');">
                        <input type="hidden" name="idSeccion" value="<?= $seccion['idSeccion']; ?>">
                        <button type="submit" name="eliminarSeccion"><img src="../../assets/img/basura.png" class="icono-AM" alt="Eliminar"></button>
                    </form>
                </div>
                <h2><?= htmlspecialchars($seccion['nombre_seccion']); ?></h2>
                <p><?= htmlspecialchars($seccion['descripcion']); ?></p>
                <a href="eventos_admin.php?idSeccion=<?= $seccion['idSeccion']; ?>" class="btn">Mostrar eventos</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay secciones registradas.</p>
    <?php endif; ?>

<?php else: ?>
    <!-- 🔹 EVENTOS DE LA SECCIÓN -->
    <a href="eventos_admin.php" class="btn" style="margin-bottom:20px;">⬅ Volver a secciones</a>
    <?php if (!empty($listaEventos)): ?>
        <?php foreach ($listaEventos as $evento): ?>
            <div class="card">
                <div class="headerCardEventos">
                    <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button><img src="../../assets/img/lapiz.png" class="icono-AM" alt="Editar"></button></a>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este evento?');">
                        <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                        <button type="submit" name="eliminarEvento"><img src="../../assets/img/basura.png" class="icono-AM" alt="Eliminar"></button>
                    </form>
                </div>
                <h2><?= htmlspecialchars($evento['nombre']); ?></h2>
                <p><strong>📅 Fecha:</strong> <?= htmlspecialchars($evento['fecha']); ?></p>
                <p><strong>🕒 Hora:</strong> <?= htmlspecialchars($evento['hora']); ?></p>
                <p><strong>📍 Lugar:</strong> <?= htmlspecialchars($evento['lugar']); ?></p>
                <a class="btn" href="inscritos.php?idE=<?= $evento['idE']; ?>">Información del evento</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay eventos en esta sección.</p>
    <?php endif; ?>
<?php endif; ?>

</div>

</body>
</html>
