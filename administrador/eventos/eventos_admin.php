<?php
session_start();
require_once __DIR__ . "../../../assets/sentenciasSQL/eventos.php";

// Verificar sesión del admin
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

$eventos = new Eventos();

// ✅ Eliminar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminarEvento'])) {
    $idEliminar = intval($_POST['idE']);
    if ($eventos->eliminarEvento($idEliminar)) {
        echo "<script>alert('Evento eliminado correctamente'); window.location='eventos_admin.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el evento');</script>";
    }
}

// ✅ Leer todos los eventos
$listaEventos = $eventos->leerEventos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Eventos</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        :root {
            --azul-oscuro: #0A1931;
            --azul-medio: #4A7FA7;
            --azul-intermedio: #1A3D63;
            --azul-claro: #B3CFE5;
            --verde-oscuro: #1b7f4d;
        }

        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f9fc;
        }

        header {
            background: var(--azul-oscuro);
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
            font-size: 1.6rem;
            text-align: center;
        }

        header a button {
            background-color: var(--azul-medio);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: 0.3s;
        }

        header a button:hover {
            background-color: var(--verde-oscuro);
            transform: scale(1.05);
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 25px;
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
            transition: transform 0.2s ease, filter 0.2s;
        }

        .headerCardEventos button:hover {
            transform: scale(1.1);
            filter: brightness(1.3);
        }

        .icono-AM {
            width: 24px;
            height: 24px;
        }

        .btn {
            display: inline-block;
            background-color: var(--azul-medio);
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.3s;
            margin-top: 15px;
        }

        .btn:hover {
            background-color: var(--verde-oscuro);
            transform: scale(1.05);
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
    <h1>Gestión de Eventos</h1>
    <div class="acciones-header">
        <a href="../perfil_admin.php"><button>Perfil</button></a>
        <a href="crear_evento.php"><button><img src="../../assets/img/subir evento.png" alt="Crear" class="icono-AM"></button></a>
    </div>
</header>

<div class="container">

<?php if (!empty($listaEventos)): ?>
    <?php foreach ($listaEventos as $evento): ?>
        <div class="card">
            <div class="headerCardEventos">
                <a href="editar_evento.php?idE=<?= $evento['idE']; ?>">
                    <button><img src="../../assets/img/lapiz.png" class="icono-AM" alt="Editar"></button>
                </a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este evento?');">
                    <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                    <button type="submit" name="eliminarEvento">
                        <img src="../../assets/img/basura.png" class="icono-AM" alt="Eliminar">
                    </button>
                </form>
            </div>

            <h2><?= htmlspecialchars($evento['nombre']); ?></h2>
            <p><strong>📅 Fecha:</strong> <?= htmlspecialchars($evento['fecha']); ?></p>
            <p><strong>🕒 Hora:</strong> <?= htmlspecialchars($evento['hora']); ?></p>
            <p><strong>📍 Lugar:</strong> <?= htmlspecialchars($evento['lugar']); ?></p>
            <a class="btn" href="inscritos.php?idE=<?= $evento['idE']; ?>">Ver información</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay eventos registrados.</p>
<?php endif; ?>

</div>

</body>
</html>
