<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/eventos.php";

if (!isset($_GET['idE'])) {
    die("Evento no especificado.");
}

$idE = intval($_GET['idE']);
$eventosObj = new Eventos();
$evento = $eventosObj->leerEventoPorId($idE); 
$inscritos = $eventosObj->verInscritos($idE);
$asistentes = $eventosObj->verAsistentes($idE); 
$estadisticas = $eventosObj->estadisticasEvento($idE);
if (!empty($estadisticas)) {
    $totalInscritos = $estadisticas[0]['total_inscritos'];
    $totalAsistentes = $estadisticas[0]['total_asistentes'];
    $porcentaje = ($totalInscritos> 0)
    ? round(($totalAsistentes / $totalInscritos) * 100, 2) 
                  : 0;
} else {
    $totalInscritos = $totalAsistentes = $porcentaje = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $idEliminar = intval($_POST['idE']);
    if ($eventosObj->eliminarEvento($idEliminar)) {
        echo "<script>alert('Evento eliminado correctamente'); window.location='eventos_admin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al eliminar el evento');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscritos</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f4f6f9;
        }
        h1, h2 {
            text-align: center;
        }
        .evento-info {
            text-align: center;
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .table-container {
            overflow-x: auto;
            margin: 0 auto;
            max-width: 1000px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .btn-back {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 15px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn-back:hover {
            background: #565e64;
        }
        /* Estilo del menú */
    .tab-menu {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }

    .tab-menu button {
      padding: 10px 20px;
      border: none;
      background: #ddd;
      cursor: pointer;
      border-radius: 6px;
      transition: 0.3s;
    }

    .tab-menu button.active {
      background: #4CAF50;
      color: white;
    }

    /* Contenedores de cada sección */
    .tab-content {
      display: none;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: #f9f9f9;
    }

    .tab-content.active {
      display: block;
    }
    /*grafica*/
    .grafica {
        width: 400px;
        height: 400px;
        margin: 0 auto;
        max-width: 400px;
        max-height: 400px;
    }
    

    </style>
</head>
<body>
    
        <button class="regresar" onclick="window.history.back()">   
            <span>Volver</span>
        </button>
    
    <h1>Informacion del evento</h1>

    <div class="evento-info">
        <h2><?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p>
            <strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?> |
            <strong>Hora:</strong> <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?> |
            <strong>Lugar:</strong> <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <div class="botones_acciones">
            <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button>✏️</button></a>
                        
                        <!-- Botón eliminar con confirmación -->
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                            <button type="submit" name="eliminar">🗑️</button>
                        </form>
        </div>
    </div>

    <div class="tab-menu">
    <button class="tab-link active" data-tab="asistentes">👥 Asistentes</button>
    <button class="tab-link" data-tab="inscripciones">📝 Inscripciones</button>
    <button class="tab-link" data-tab="estadisticas">📊 Estadísticas</button>
    </div>

    <div id="asistentes" class="tab-content active">
    <h2>Lista de Asistentes</h2>
    <div class="table-container">
        <?php if (!empty($asistentes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Registro</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Fecha y hora de Asistencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistentes as $usuario): 
                        $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellidos'];
                        $telefonoCompleto = $usuario['lada'] . ' ' . $usuario['telefono'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= $nombreCompleto?></td>
                            <td><?= $telefonoCompleto?></td>
                            <td><?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($usuario['fecha_asistencia'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No hay asistentes registrados en este evento.</p>
        <?php endif; ?>
  </div>
    </div>

  <div id="inscripciones" class="tab-content">
    <h2>Inscripciones</h2>
    <div class="table-container">
        <?php if (!empty($inscritos)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Registro</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $usuario): 
                        $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellidos'];
                        $telefonoCompleto = $usuario['lada'] . ' ' . $usuario['telefono'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= $nombreCompleto?></td>
                            <td><?= $telefonoCompleto?></td>
                            <td><?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No hay usuarios inscritos en este evento.</p>
        <?php endif; ?>
    </div>
  </div>

  <div id="estadisticas" class="tab-content">
    <h2>📊 Estadísticas del evento</h2>

    <p><strong>Total inscritos:</strong> <?= $totalInscritos ?></p>
    <p><strong>Total asistentes:</strong> <?= $totalAsistentes ?></p>
    <p><strong>Porcentaje de asistencia:</strong> <?= $porcentaje ?>%</p>
    <div class="grafica">
        <br>
    <canvas id="grafica" ></canvas>
    </div>
    <script>
        const ctx = document.getElementById('grafica');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [ 'Asistentes','Inscritos'],
                datasets: [{
                    data: [<?= $totalAsistentes ?>,<?= $totalInscritos ?>],
                    backgroundColor: ['#004883ff', '#25af25ff']
                }]
            },
            
        });
    </script>
  </div>
<script>
    // para cambiar entre pestañas
    document.querySelectorAll(".tab-link").forEach(button => {
      button.addEventListener("click", () => {
        const tab = button.dataset.tab;

        // Quitar "active" de todos los botones
        document.querySelectorAll(".tab-link").forEach(btn => btn.classList.remove("active"));
        // Poner "active" al botón pulsado
        button.classList.add("active");

        // Ocultar todos los contenidos
        document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));
        // Mostrar el contenido correspondiente
        document.getElementById(tab).classList.add("active");
      });
    });
  </script>
    
</body>
</html>
