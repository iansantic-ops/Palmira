<?php
//ya esta este archivo inicial terminado
session_start();
require_once __DIR__. "../../assets/sentenciasSQL/admin.php";
$error = ""; 

if (isset($_POST['iniciar'])&& !empty($_POST['usuario']) && !empty($_POST['contrasena'])) {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);
    $admin = new Admin();
    $adminData = $admin->leerAdmin($usuario, $contrasena);
        if ($adminData) {
            // esto crea la sesión
            echo "sesion iniciada";
            $_SESSION['idAdmin'] = $adminData['idAdmin'];
            $_SESSION['usuario'] = $adminData['usuario'];
            header("Location: menu_admin.php"); 
            exit();
        } elseif($adminData === false) {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
    <link rel="stylesheet" href="../assets/css/formularios.css">
</head>
<body>
    <div class="wrapper">
        <form action="" method="post">
            <h1>Iniciar sesión</h1>
<br>
            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;"><?= $error ?></p>
            <?php endif; ?>

            <div class="input-box">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" required>
            </div>

            <div class="input-box">
                <label for="contrasena">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" required>
            </div>
            <br>
            <div class="button-group">
                <button type="submit" name="iniciar" class="btn">Iniciar sesión</button>
            </div>
        </form>
    </div>
</body>
</html>
