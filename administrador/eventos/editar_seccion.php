<?php
session_start();
require_once __DIR__ . "../../../assets/sentenciasSQL/secciones.php";

// Bloquear acceso si no hay sesión de admin
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

$secciones = new Secciones();

// Obtener el id de la sección a editar
if (!isset($_GET['idSeccion'])) {
    header("Location: crear_seccion.php");
    exit();
}

$idSeccion = intval($_GET['idSeccion']);
$seccion   = $secciones->obtenerSeccionPorId($idSeccion);

if (!$seccion) {
    echo "<script>alert('Sección no encontrada'); window.location='eventos_admin.php';</script>";
    exit();
}

// Procesar formulario de actualización
if (isset($_POST['actualizar'])) {
    $nombre      = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');

    $resultado = $secciones->actualizarSeccion($idSeccion, $nombre, $descripcion);

    if ($resultado) {
        echo "<script>alert('Sección actualizada correctamente'); window.location='eventos_admin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar la sección');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sección</title>
    
    <link rel="stylesheet" href="../../assets/css/eventos.css">
</head>
<body>
    <header>
        <h2>Editar Sección</h2>
        <button onclick="window.history.back()">Volver</button>
    </header>

    <form action="" method="POST">
        <label for="nombre">Nombre de la Sección:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($seccion['nombre_seccion']); ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" rows="4"><?= htmlspecialchars($seccion['descripcion']); ?></textarea>

        <br><br>
        <button type="submit" name="actualizar">Guardar Cambios</button>
    </form>
</body>
</html>
