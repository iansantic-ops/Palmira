<?php
session_start();
require_once __DIR__. "../../../assets/sentenciasSQL/eventos.php";
// Verificar si el admin inició sesión
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php"); // Redirige al login si no hay sesión activa
    exit();
}
$eventos =new Eventos();
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
    <title>Eventos</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f6f9;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card h2 {
            margin: 0 0 10px;
            color: #333;
        }
        .card p {
            margin: 5px 0;
            color: #555;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .panel-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 12px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .panel-link:hover {
            background: #1e7e34;
        }
    </style>
</head>
<body>
    <header>
     <a href="../menu_admin.php"><button>Volver al menu</button></a>
    <h1>Lista de Eventos</h1>
    <a href="../perfil_admin.php"><button>Perfil</button></a>
    <a href="crear_evento.php"><button>+</button></a>
    </header>

    <div class="container">
        <?php if (!empty($listaEventos)): ?>
            <?php foreach ($listaEventos as $evento): ?>
                <div class="card">
                    <div class="headerCardEventos">
                        <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button>✏️</button></a>
                        
                        <!-- Botón eliminar con confirmación -->
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                            <button type="submit" name="eliminar">🗑️</button>
                        </form>
                    </div>

                    <h2><?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Hora:</strong> <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Lugar:</strong> <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="btn" href="inscritos.php?idE=<?= $evento['idE']; ?>">Info</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay eventos registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>


