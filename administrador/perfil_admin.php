<?php
session_start();
require_once "../assets/sentenciasSQL/admin.php";
$adminModel = new Admin();

// Si ya está logueado
if (isset($_SESSION['idAdmin'])) {
    $perfil = $adminModel->obtenerAdminPorId($_SESSION['idAdmin']);
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Administrador</title>
    <link rel="stylesheet" href="../assets/css/perfiladmin.css">
</head>
<body>
<header>
<h2>Perfil del Administrador</h2>
<button class="regresar" onclick="window.history.back()">   
            <span>Volver</span>
        </button>
</header>
<main>

    <div class="MUsuario">
        
        <p><strong>ID:</strong> <?= htmlspecialchars($perfil['idAdmin']); ?></p>
        <p><strong>Usuario:</strong> <?= htmlspecialchars($perfil['usuario']); ?></p>

        <div class="botones-usuario">
            <a href="modificar_admin.php?id=<?= $perfil['idAdmin']; ?>">
                <button class="btn-accion">
                    Editar Perfil 
                    <img src="../assets/img/lapiz-de-usuario.png" alt="Editar" class="imagenes_acciones">
                </button>
            </a>

            <a href="logout.php">
                <button class="btn-cerrar">
                    Cerrar Sesión 
                    <img src="../assets/img/cierre-de-sesion-de-usuario.png" alt="Cerrar" class="imagenes_acciones">
                </button>
            </a>
        </div>
    </div>

</body>
</html>
