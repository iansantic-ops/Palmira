<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: index.php");
    exit();
}

require_once "../assets/sentenciasSQL/conexion.php";

try {
    $stmt = $pdo->query("SELECT imagen_anuncio FROM configuracion WHERE id=1");
    $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $imagenActual = $anuncio && !empty($anuncio['imagen_anuncio'])
        ? '../' . $anuncio['imagen_anuncio']  
        : '../assets/img/default.jpg';        
} catch (Exception $e) {
    $imagenActual = '../assets/img/default.jpg';
}

if (isset($_POST['subir']) && isset($_FILES["imagen"])) {
    try {
        $carpetaDestino = "../assets/anuncioPalmira/"; 
        if (!is_dir($carpetaDestino)) {
            mkdir($carpetaDestino, 0777, true); 
        }
        $nombreArchivo = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaDestino = $carpetaDestino . $nombreArchivo;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
            $rutaBD = "assets/anuncioPalmira/" . $nombreArchivo;
            $sql = "UPDATE configuracion SET imagen_anuncio = :imagen WHERE id = 1";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([':imagen' => $rutaBD])) {
                echo "<p style='color:green;'> Imagen subida y guardada correctamente.</p>";
                $imagenActual = '../' . $rutaBD;

                // Guardar en historial
                $sqlHist = "INSERT INTO anuncios_historial (ruta_imagen) VALUES (:imagen)";
                $stmtHist = $pdo->prepare($sqlHist);
                $stmtHist->execute([':imagen' => $rutaBD]);
            } else {
                echo "<p style='color:red;'> No se pudo guardar la imagen.</p>";
            }
        } else {
            echo "<p style='color:red;'> No se pudo mover el archivo. Revisa permisos.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'> Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Anuncio</title>
    <style>
        .preview { margin: 20px 0; }
        .preview img { max-width: 300px; max-height: 300px; border: 2px solid #ccc; border-radius: 10px; }
    </style>
</head>
<body>
     <button class="regresar" onclick="window.history.back()">   
            <span>Volver</span>
        </button>
    <h1>Panel Administrador</h1>
    <h2>Imagen de Anuncio Actual</h2>

    <div class="preview">
        <img src="<?php echo $imagenActual; ?>" alt="Anuncio actual">
    </div>

    <form method="POST" enctype="multipart/form-data">
        <label>Subir nueva imagen:</label><br><br>
        <input type="file" name="imagen" accept="image/*" required>
        <br><br>
        <button type="submit" name="subir">Actualizar Anuncio</button>
    </form>
    
</body>
</html>

