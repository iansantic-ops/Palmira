<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once "../assets/sentenciasSQL/conexion.php";

$mensaje = "";
$idSeleccionado = $_GET['id'] ?? null;

// Actualizar admin
if (isset($_POST['modificar'])) {
    $id = intval($_POST['idAdmin']);
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    $stmt = $pdo->prepare("UPDATE admin SET usuario = :usuario, contrasena = :contrasena WHERE idAdmin = :id");
    $ok = $stmt->execute([':usuario' => $usuario, ':contrasena' => $contrasena, ':id' => $id]);

    $mensaje = $ok ? "Administrador actualizado correctamente." : "Error al actualizar.";
}

// Obtener todos los admins
$stmt = $pdo->query("SELECT * FROM admin ORDER BY idAdmin ASC");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos del admin seleccionado
$adminSel = null;
if ($idSeleccionado) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE idAdmin = :id");
    $stmt->execute([':id' => $idSeleccionado]);
    $adminSel = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Administrador</title>
    <link rel="stylesheet" href="../assets/css/eventos.css">
    <style>
        body { font-family: Arial; margin: 20px; background: #f4f6f9; }
        h1 { text-align: center; }
        .mensaje { text-align:center; color: green; margin-bottom: 20px; }
        .container { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        form { text-align: center; margin-top: 20px; }
        input { margin: 5px; padding: 8px; border-radius: 8px; border: 1px solid #ccc; }
        button { padding: 8px 12px; background: #007bff; color: #fff; border-radius: 8px; border: none; }
    </style>
</head>
<body>
    <header>
        <h2>Modificar Administrador</h2>
        <button class="regresar" onclick="window.history.back()">Volver</button>

    </header>
    <main>
    

    <?php if ($mensaje): ?>
        <p class="mensaje"><?= $mensaje ?></p>
    <?php endif; ?>

    <?php if (!$idSeleccionado): ?>
        <div class="container">
            <?php foreach ($admins as $admin): ?>
                <div class="card">
                    <h2><?= htmlspecialchars($admin['usuario']); ?></h2>
                    <p><strong>ID:</strong> <?= $admin['idAdmin']; ?></p>
                    <a href="?id=<?= $admin['idAdmin']; ?>">Modificar</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="idAdmin" value="<?= $adminSel['idAdmin']; ?>">
            <input type="text" name="usuario" value="<?= htmlspecialchars($adminSel['usuario']); ?>" required><br>
            <input type="password" name="contrasena" placeholder="Nueva contraseña" required><br>
            <button type="submit" name="modificar">Guardar Cambios</button>
        </form>
    <?php endif; ?>
</body>
</html>
