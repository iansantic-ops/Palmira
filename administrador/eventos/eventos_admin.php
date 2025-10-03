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
        
.container {
  display: flex;
  flex-wrap: wrap; 
  justify-content: center;  
  gap: 20px; 
  padding: 20px;
}

/* Tarjetas individuales */
.card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  padding: 20px;
  width: 300px; /* puedes ajustar el ancho */
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.headerCardEventos {
  display: flex;
  justify-content: center; /* centra los botones horizontalmente */
  align-items: center;
  gap: 20px; /* espacio entre los botones */
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

/* Botón inferior de información */
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

    </style>
</head>
<body>
    <header>
     <a href="../menu_admin.php"><button>Volver al menu</button></a>
    <h1>Lista de Eventos</h1>
    <a href="../perfil_admin.php"><button>Perfil</button></a>
    <a href="crear_evento.php"><button><img src="../../assets/img/subir evento.png" alt="Icono PNG" class="icono-AM"></button></a>
    </header>

    <div class="container">
        <?php if (!empty($listaEventos)): ?>
            <?php foreach ($listaEventos as $evento): ?>
                <div class="card">
                    <div class="headerCardEventos">
                        <a href="editar_evento.php?idE=<?= $evento['idE']; ?>"><button><img src="../../assets/img/lapiz.png" alt="Icono PNG" class="icono-AM"></button></a>
                        
                        <!-- Botón eliminar con confirmación -->
                        <form method="POST" style="display:inline;" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            <input type="hidden" name="idE" value="<?= $evento['idE']; ?>">
                            <button type="submit" name="eliminar"><img src="../../assets/img/basura.png" alt="Icono PNG" class="icono-AM"></button>
                        </form>
                    </div>

                    <h2><?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><strong><img src="../../assets/img/calendario.png" alt="Icono PNG" class="icono-AM"> Fecha:</strong> <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><img src="../../assets/img/reloj.png" alt="Icono PNG" class="icono-AM"> Hora:</strong> <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong><img src="../../assets/img/marcador.png" alt="Icono PNG" class="icono-AM"> Lugar:</strong> <?= htmlspecialchars($evento['lugar'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="btn" href="inscritos.php?idE=<?= $evento['idE']; ?>">Informacion del evento</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay eventos registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>


