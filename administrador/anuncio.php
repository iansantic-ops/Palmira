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
                echo "<p style='color:lightgreen; text-align:center;'>✅ Imagen subida y guardada correctamente.</p>";
                $imagenActual = '../' . $rutaBD;

                // Guardar en historial
                $sqlHist = "INSERT INTO anuncios_historial (ruta_imagen) VALUES (:imagen)";
                $stmtHist = $pdo->prepare($sqlHist);
                $stmtHist->execute([':imagen' => $rutaBD]);
            } else {
                echo "<p style='color:red; text-align:center;'>❌ No se pudo guardar la imagen.</p>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>⚠️ No se pudo mover el archivo. Revisa permisos.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red; text-align:center;'> Error: " . $e->getMessage() . "</p>";
    }
}

// Manejar eliminación de un anuncio del historial (ocultar de la vista de usuarios)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_historial'])) {
    $rutaEliminar = $_POST['ruta'] ?? '';
    if (!empty($rutaEliminar)) {
        try {
            $sqlDel = "DELETE FROM anuncios_historial WHERE ruta_imagen = :ruta LIMIT 1";
            $stmtDel = $pdo->prepare($sqlDel);
            if ($stmtDel->execute([':ruta' => $rutaEliminar])) {
                echo "<p style='color:lightgreen; text-align:center;'>✅ Anuncio eliminado del historial correctamente.</p>";
            } else {
                echo "<p style='color:red; text-align:center;'>❌ No se pudo eliminar el anuncio del historial.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red; text-align:center;'> Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Anuncio</title>
    <link rel="stylesheet" href="../assets/css/anuncio.css">
    
</head>
<body>
    <header>
        <h2>Cambiar Anuncio</h2>
        <a href="menu_admin.php"><button>Volver</button></a>
    </header>

    <main>
        <h2>Anuncio Actual:</h2>
        <br>
        <div class="preview">
            <img src="<?php echo $imagenActual; ?>" alt="Anuncio actual">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label>Subir nueva imagen:</label>
            <input type="file" name="imagen" accept="image/*" required>
            <button type="submit" name="subir">Actualizar Anuncio</button>
        </form>

        <hr>
        <h3>Historial de anuncios</h3>
        <div class="historial-admin" style="display:flex;flex-wrap:wrap;gap:12px;">
            <?php
            try {
                $stmtH = $pdo->query("SELECT ruta_imagen, fecha FROM anuncios_historial ORDER BY fecha DESC");
                $lista = $stmtH->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $lista = [];
            }
            if (!empty($lista)) {
                foreach ($lista as $item) {
                    $ruta = $item['ruta_imagen'];
                    $fecha = $item['fecha'];
                    echo "<div style='width:180px;border:1px solid #ddd;padding:8px;border-radius:8px;text-align:center;'>";
                    echo "<img src='../".htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8')."' style='max-width:160px;border-radius:6px;'><br>";
                    echo "<small>".htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8')."</small><br>";
                    echo "<form method='POST' onsubmit='return confirm(\'¿Eliminar este anuncio de la vista de usuarios?\');' style='margin-top:6px;'>";
                    echo "<input type='hidden' name='ruta' value='".htmlspecialchars($ruta, ENT_QUOTES, 'UTF-8')."'>";
                    echo "<button type='submit' name='eliminar_historial' style='background:#c62828;color:#fff;border:none;padding:6px 8px;border-radius:6px;cursor:pointer;'>Eliminar</button>";
                    echo "</form></div>";
                }
            } else {
                echo "<p>No hay historial de anuncios.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
