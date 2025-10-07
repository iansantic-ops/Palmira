<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location:index.php");
    exit();
}
require_once "./assets/sentenciasSQL/conexion.php";

try {
    $stmtHist = $pdo->query("SELECT ruta_imagen, fecha FROM anuncios_historial ORDER BY fecha DESC");
    $anuncios = $stmtHist->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $anuncios = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Anuncios Anteriores</title>
    <link rel="stylesheet" href="./assets/css/todo.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <style>
        .historial { display: flex; gap: 15px; flex-wrap: wrap; }
        .historial img { max-width: 150px; border: 1px solid #ccc; border-radius: 5px; }
        
    .iconoT1 {
      width: 20px;   
      height: 20px;  
      object-fit: contain;      
      image-rendering: crisp-edges; 
    }
  
    </style>
</head>
<body>
    <div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles <img src="./assets/img/eventos.png" alt="Icono PNG" class="iconoT1"></a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores <img src="./assets/img/anuncio.png" alt="Icono PNG" class="iconoT1"></a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil <img src="./assets/img/usuario.png" alt="Icono PNG" class="iconoT1"></a></div>

    <svg
      class="outline"
      viewBox="0 0 400 60"
      xmlns="http://www.w3.org/2000/svg"
      preserveAspectRatio="xMidYMid meet"
    >
      <rect
        class="rect"
        pathLength="100"
        x="0"
        y="0"
        width="400"
        height="60"
        fill="transparent"
        stroke-width="4"
      ></rect>
    </svg>
  </div>
</div>
<br>
    <header>
        <h1>Novedades</h1>
        
    </header>
<br>
    <div class="historial">
    <?php
    if ($anuncios) {
        foreach ($anuncios as $item) {
            $ruta = $item['ruta_imagen'];
            echo "<div class='historial-item'>
                    <img src='{$ruta}' alt='Anuncio'>
                    <p>{$item['fecha']}</p>
                  </div>";
        }
    } else {
        echo "<p>No hay anuncios anteriores.</p>";
    }
    ?>
</div>
<div id="overlay">
        <span class="cerrar">&times;</span>
        <img src="" alt="Imagen Grande">
    </div>

    <script>
        const overlay = document.getElementById('overlay');
        const overlayImg = overlay.querySelector('img');
        const cerrar = overlay.querySelector('.cerrar');

        document.querySelectorAll('.historial-item img').forEach(img => {
            img.addEventListener('click', () => {
                overlay.style.display = 'flex';
                overlayImg.src = img.src;
            });
        });

        cerrar.addEventListener('click', () => {
            overlay.style.display = 'none';
        });

        // Cerrar al hacer clic fuera de la imagen
        overlay.addEventListener('click', (e) => {
            if(e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    </script>
</body>
</html>
