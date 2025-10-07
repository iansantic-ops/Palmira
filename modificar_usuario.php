<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("Location:index.php");
    exit();
}

require_once __DIR__ . "/assets/sentenciasSQL/usuarios.php"; 

$idUsuario = $_SESSION['idUsuario'];
$usuariosObj = new Usuarios();
$usuario = $usuariosObj->buscarUsuarioPorId($idUsuario);

$mensaje = "";
if (isset($_POST['modificar'])) {
    $nombre    = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $apellidos = htmlspecialchars(trim($_POST['apellidos']), ENT_QUOTES, 'UTF-8');
    $telefono  = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
    $correo    = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
    $origen    = htmlspecialchars(trim($_POST['origen']), ENT_QUOTES, 'UTF-8');
    $pais      = htmlspecialchars(trim($_POST['pais']), ENT_QUOTES, 'UTF-8');

    $actualizado = $usuariosObj->modificarUsuario($idUsuario, $nombre, $apellidos, $telefono, $correo, $origen, $pais);
    if ($actualizado) {
        $mensaje = "Datos actualizados correctamente.";
        $_SESSION['nombre'] = $nombre;
        $_SESSION['correo'] = $correo;
        $usuario = $usuariosObj->buscarUsuarioPorId($idUsuario);
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
}
$paisSeleccionado = $usuario['pais'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="./assets/css/formularioM.css">
    <script src="assets/js/validacion.js"></script>
</head>
<body class="modificar-usuario">


<div class="wrapper">
    <h1>Modificar datos personales</h1>

    <?php if (!empty($mensaje)): ?>
        <p style="color:green; text-align:center;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form action="" method="post">

        <div class="input-box">
            <input type="text" id="nombre" name="nombre"
                   value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Nombre" required>
        </div>

        <div class="input-box">
            <input type="text" id="apellidos" name="apellidos"
                   value="<?= htmlspecialchars($usuario['apellidos'], ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Apellidos" required>
        </div>

        <div class="input-box">
            <input type="text" id="telefono" name="telefono"
                   value="<?= htmlspecialchars($usuario['telefono'], ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Teléfono" required>
        </div>

        <div class="input-box">
            <input type="email" id="correo" name="correo"
                   value="<?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Correo electrónico" required>
        </div>

        <div class="input-box">
            <input type="text" id="origen" name="origen"
                   value="<?= htmlspecialchars($usuario['origen'], ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Institución de origen" required>
        </div>

        <div class="input-box">
            <select name="pais" id="pais" required>
                <?php
                $paises = [
                    "CANADÁ","ESTADOS UNIDOS","MÉXICO","GUATEMALA","EL SALVADOR",
                    "HONDURAS","NICARAGUA","COSTA RICA","PANAMÁ","CUBA",
                    "REPÚBLICA DOMINICANA","JAMAICA","HAITÍ",
                    "ARGENTINA","BOLIVIA","BRASIL","CHILE","COLOMBIA",
                    "ECUADOR","PARAGUAY","PERÚ","URUGUAY","VENEZUELA",
                    "ESPAÑA","FRANCIA","ALEMANIA","ITALIA","REINO UNIDO",
                    "PAÍSES BAJOS","RUSIA","PORTUGAL","SUIZA","SUECIA",
                    "JAPÓN","CHINA","INDIA","COREA DEL SUR","ARABIA SAUDITA",
                    "AUSTRALIA","NUEVA ZELANDA",
                    "SUDÁFRICA","EGIPTO","NIGERIA","MARRUECOS",
                    "OTRO..."
                ];
                sort($paises);
                foreach ($paises as $opcion) {
                    $selected = ($paisSeleccionado === $opcion) ? 'selected' : '';
                    echo "<option value=\"$opcion\" $selected>$opcion</option>";
                }
                ?>
            </select>
        </div>

        <button class="btn" type="submit" name="modificar">Actualizar datos</button>
    </form>

    <br>
    <a href="perfil_usuario.php">
        <button type="button" class="btn"> Volver al perfil</button>
    </a>
</div>

</body>
</html>
