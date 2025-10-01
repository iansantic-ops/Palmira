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
</head>
<body>
    <h1>Perfil del Administrador</h1>
    <p><strong>ID:</strong> <?= htmlspecialchars($perfil['idAdmin']); ?></p>
    <p><strong>Usuario:</strong> <?= htmlspecialchars($perfil['usuario']); ?></p>
    <a href="modificar_admin.php?id=<?= $perfil['idAdmin']; ?>"><button>Editar Perfil</button></a>

    <a href="logout.php"><button>cerrar sesion</button></a>
</body>
</html>
