<?php
//este archivo es el menu principal del admin y tmb ya esta terminado
session_start();

// Verificar si existe sesión
if (!isset($_SESSION['idAdmin'])) {
    header("Location: index.php"); // Redirige al login si no hay sesión activa
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header>
        <h2>Panel de Administración</h2>
        <a href="perfil_admin.php" class="boton-perfil">
    Perfil
    <img class="imagenes_acciones" src="../assets/img/usuario.png" alt="Icon">
</a>

    </header>
<main>
<h1>Panel de Administración</h1>
<p>👤 Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>

<a href="eventos/eventos_admin.php" class="boton">Eventos</a>
<a href="anuncio.php" class="boton">Anuncio</a>
<a href="asistencia/QR.php" class="boton" >Marcar asistencias por evento</a>
<a href="usuarios/usuarios_registrados.php" class="boton" >usuarios en plataforma</a>
<br><br>
<a href="logout.php" class="boton">Cerrar sesión</a>
</main>
</body>
</html>
