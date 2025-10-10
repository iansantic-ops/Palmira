<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}
include_once __DIR__ . ("../../../assets/sentenciasSQL/secciones.php");

if (isset($_POST['crear'])) {
    $nombre = htmlspecialchars(trim($_POST['nombre_seccion']), ENT_QUOTES, 'UTF-8');
    $descripcion = htmlspecialchars(trim($_POST['descripcion']), ENT_QUOTES, 'UTF-8');

    $crear_seccion = new Secciones();
    $crear = $crear_seccion->crearSeccion($nombre, $descripcion);

    if ($crear === true) {
        echo "<script>alert('Sección creada correctamente'); window.location='eventos_admin.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al crear la sección');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Sección</title>
<link rel="stylesheet" href="../../assets/css/eventos.css">
</head>
<body>
<header>
    <h2>Crear Sección</h2>
    <button class="regresar" onclick="window.history.back()">Volver</button>
</header>

<form method="POST" action="crear_seccion.php">
    <label>Nombre de la sección:</label>
    <input type="text" name="nombre_seccion" required>

    <label>Descripción:</label>
    <textarea name="descripcion"></textarea>

    <br><br>
    <button type="submit" name="crear">Crear Sección</button>
</form>
</body>
</html>
