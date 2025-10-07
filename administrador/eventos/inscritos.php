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
    <link rel="stylesheet" href="../../assets/css/inscritos.css">
    <style>
        .evento-info button {
  background: none;
  border: none;
  cursor: pointer;
  transition: transform 0.2s ease;
}
        .icono-EM {
  width: 24px;
  height: 24px;
}
.icono-Estrella {
      width: 22px;   
      height: 22px;  
      object-fit: contain;      
      image-rendering: crisp-edges; 
    }
        </style>
</head>
<body>
    <header>
        <h1>Informacion del evento</h1>

        <a href="eventos_admin.php"><button>Volver</button></a>
</header>
    <main>

    <div class="evento-info">
        <h2><?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <br>
        <p>
            <strong>Fecha:</strong> <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?> |
            <strong>Hora:</strong> <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?> |
            <strong>Lugar:</strong> <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <div class="botones_acciones">
            <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button><img src="../../assets/img/lapiz.png" alt="Icono PNG" class="icono-EM"></button></a>ㅤ
                        
                        <!-- Botón eliminar con confirmación -->
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                            <button type="submit" name="eliminar"><img src="../../assets/img/basura.png" alt="Icono PNG" class="icono-EM"></button>
                        </form>
        </div>
    </div>

    <div class="tab-menu">
    <button class="tab-link active" data-tab="asistentes">Asistentes <img src="../../assets/img/revisar.png" alt="Icono PNG" class="icono-Estrella"></button>
    <button class="tab-link" data-tab="inscripciones">Inscripciones <img src="../../assets/img/usuarios.png" alt="Icono PNG" class="icono-Estrella"></button>
    <button class="tab-link" data-tab="estadisticas">Estadísticas <img src="../../assets/img/estadisticas.png" alt="Icono PNG" class="icono-Estrella"></button>
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
    <h2>Estadísticas del evento</h2>
<br>    
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
                    backgroundColor: ['#4A7FA7', '#1b7f4d ']
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
