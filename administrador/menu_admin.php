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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<style>
    .icono-A {
      width: 27px;   
      height: 27px;  
      object-fit: contain;      
      image-rendering: crisp-edges; 
    }
  </style>
<body>
    <header>
        <h2>Panel de Administración</h2>
        <a href="perfil_admin.php" class="boton-perfil">
    Perfil
    <img class="imagenes_acciones" src="../assets/img/usuario.png" alt="Icon">
</a>

    </header>
<main>

<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>

<div class="contenedor-botones">
  <a href="eventos/eventos_admin.php" class="boton">Eventosㅤ<img src="../assets/img/eventos.png" alt="Icono PNG" class="icono-A"></a>
  <a href="anuncio.php" class="boton">Anuncio ㅤ<img src="../assets/img/anuncio.png" alt="Icono PNG" class="icono-A"></a>
  <a href="asistencia/QR.php" class="boton">Marcar asistencias por evento <img src="../assets/img/asistencia.png" alt="Icono PNG" class="icono-A"></a>
  <a href="usuarios/usuarios_registrados.php" class="boton">Usuarios en plataforma<img src="../assets/img/usuarios.png" alt="Icono PNG" class="icono-A"></a>
  </div>

</main>
</body>
</html>
