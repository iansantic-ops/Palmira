<?php
session_start();
require_once __DIR__ . "../../../assets/sentenciasSQL/eventos.php";

// Verificar si el admin inició sesión
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

$eventos = new Eventos();

// Eliminar evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $idEliminar = intval($_POST['idE']);
    if ($eventos->eliminarEvento($idEliminar)) {
        echo "<script>alert('Evento eliminado correctamente'); window.location='eventos_admin.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el evento');</script>";
    }
}

$listaEventos = $eventos->leerEventos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        /* ====== ESTRUCTURA GENERAL ====== */
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

        /* ====== BUSCADOR ====== */
        .search-bar {
            margin: 20px auto;
            text-align: center;
        }

        .search-bar input {
            width: 80%;
            max-width: 400px;
            padding: 10px;
            font-size: 1rem;
            border: 2px solid #4A7FA7;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            border-color: #1b7f4d;
            box-shadow: 0 0 5px rgba(27,127,77,0.3);
        }

        /* ====== CONTENEDOR ====== */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 20px;
        }

        /* ====== TARJETAS ====== */
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

        /* ====== RESPONSIVE ====== */
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

            .search-bar input {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="../menu_admin.php"><button>Volver al menú</button></a>
        <h1>Lista de Eventos</h1>
        <div class="acciones-header">
            <a href="../perfil_admin.php"><button>Perfil</button></a>
            <a href="crear_evento.php"><button><img src="../../assets/img/subir evento.png" alt="Crear" class="icono-AM"></button></a>
        </div>
    </header>

    <div class="search-bar">
        <input type="text" id="busqueda" placeholder="🔍 Buscar evento por nombre o lugar...">
    </div>

    <div class="container" id="contenedor-eventos">
        <?php if (!empty($listaEventos)): ?>
            <?php foreach ($listaEventos as $evento): ?>
                <div class="card" data-nombre="<?= strtolower($evento['nombre']); ?>" data-lugar="<?= strtolower($evento['lugar']); ?>">
                    <div class="headerCardEventos">
                        <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button><img src="../../assets/img/lapiz.png" class="icono-AM" alt="Editar"></button></a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                            <button type="submit" name="eliminar"><img src="../../assets/img/basura.png" class="icono-AM" alt="Eliminar"></button>
                        </form>
                    </div>

                    <h2><?= htmlspecialchars($evento['nombre']); ?></h2>
                    <p><strong><img src="../../assets/img/calendario.png" class="icono-AM"> Fecha:</strong> <?= htmlspecialchars($evento['fecha']); ?></p>
                    <p><strong><img src="../../assets/img/reloj.png" class="icono-AM"> Hora:</strong> <?= htmlspecialchars($evento['hora']); ?></p>
                    <p><strong><img src="../../assets/img/marcador.png" class="icono-AM"> Lugar:</strong> <?= htmlspecialchars($evento['lugar']); ?></p>
                    <a class="btn" href="inscritos.php?idE=<?= $evento['idE']; ?>">Información del evento</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay eventos registrados.</p>
        <?php endif; ?>
    </div>

    <script>
        // 🔎 Filtro de búsqueda en tiempo real
        const inputBusqueda = document.getElementById('busqueda');
        const tarjetas = document.querySelectorAll('.card');

        inputBusqueda.addEventListener('keyup', () => {
            const texto = inputBusqueda.value.toLowerCase();
            tarjetas.forEach(card => {
                const nombre = card.dataset.nombre;
                const lugar = card.dataset.lugar;
                card.style.display = (nombre.includes(texto) || lugar.includes(texto)) ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>
