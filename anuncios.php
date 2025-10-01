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
    </style>
</head>
<body>
    <div class="nav">
  <div class="container">
    <div class="btn"><a href="eventos_disponibles.php">Eventos Disponibles</a></div>
    <div class="btn"><a href="anuncios.php">Anuncios anteriores</a></div>
    <div class="btn"><a href="perfil_usuario.php">Ir al perfil</a></div>

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

</body>
</html>
