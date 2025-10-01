<?php
session_start();
if(!isset($_SESSION['idAdmin'])){
    header("Location: ../index.php");
    exit();
}
require_once "../../assets/sentenciasSQL/usuarios.php";
$Usuarios = new Usuarios(); 
if(isset($_POST['idR'])){
    $idR = intval($_POST['idR']);
    $usuario = $Usuarios->buscarUsuarioPorId($idR);
} else {
    header("Location: usuarios_registrados.php");
    exit();
}
if(isset($_POST['modificar'])){
    if(isset($_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['apellidos'], $_POST['origen'], $_POST['pais'])) { 
    $nombre    = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
    $apellidos = htmlspecialchars(trim($_POST['apellidos']), ENT_QUOTES, 'UTF-8');
    $telefono  = htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8');
    $correo    = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
    $origen    = htmlspecialchars(trim($_POST['origen']), ENT_QUOTES, 'UTF-8');
    $pais      = htmlspecialchars(trim($_POST['pais']), ENT_QUOTES, 'UTF-8');

    $actualizado = $Usuarios->modificarUsuario($idR, $nombre, $apellidos, $telefono, $correo, $origen, $pais);
    if($actualizado) {
        echo "<script>alert('Usuario modificado correctamente'); window.location='usuarios_registrados.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al modificar el usuario');</script>";
    }
}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar usuario</title>
    <script src="../../assets/js/validacion.js"></script>
</head>
<body>
    <header>
        <h2>Modificar usuario</h2>
        <a href="usuarios_registrados.php"><button>Volver</button></a>
    </header>
    <main>
        <form method="POST" action="modificar_usuario.php">
            <input type="hidden" id="idR" name="idR" value="<?= htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?>">
            <label>Nombre:
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label><br>
            <label>Apellidos:
                <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label><br>
            <label>Correo:
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label><br>
            <label>Teléfono:
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($usuario['telefono'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </label><br>
            <label>Origen:
                <input type="text" id="origen" name="origen" value="<?= htmlspecialchars($usuario['origen'], ENT_QUOTES, 'UTF-8'); ?>">
            </label><br>
            <?php 
    $paisSeleccionado = $usuario['pais'];
    ?>
    <label>País:</label>
    <select name="pais" id="pais" required>
                <?php
                $paises = [
                    // América del Norte y Central
                    "CANADÁ", "ESTADOS UNIDOS", "MÉXICO", "GUATEMALA", "EL SALVADOR", 
                    "HONDURAS", "NICARAGUA", "COSTA RICA", "PANAMÁ", "CUBA", 
                    "REPÚBLICA DOMINICANA", "JAMAICA", "HAITÍ",
                
                    // América del Sur
                    "ARGENTINA", "BOLIVIA", "BRASIL", "CHILE", "COLOMBIA", 
                    "ECUADOR", "PARAGUAY", "PERÚ", "URUGUAY", "VENEZUELA",
                
                    // Europa
                    "ESPAÑA", "FRANCIA", "ALEMANIA", "ITALIA", "REINO UNIDO",
                    "PAÍSES BAJOS", "RUSIA", "PORTUGAL", "SUIZA", "SUECIA",
                
                    // Asia
                    "JAPÓN", "CHINA", "INDIA", "COREA DEL SUR", "ARABIA SAUDITA",
                
                    // Oceanía
                    "AUSTRALIA", "NUEVA ZELANDA",
                
                    // África
                    "SUDÁFRICA", "EGIPTO", "NIGERIA", "MARRUECOS",
                
                    // Otros
                    "OTRO..."
                ];

                sort($paises);

                foreach ($paises as $opcion) {
                    $selected = (isset($pais) && $pais === $opcion) ? 'selected' : '';
                    if (!isset($pais) && $paisSeleccionado === $opcion) {
                        $opcion = $paisSeleccionado; // Mantener el país actual si no se ha enviado el formulario
                        $selected = 'selected';
                    }

                    echo "<option value=\"$opcion\" $selected>$opcion</option>";
                }
                ?>
            </select><br><br>
            <button type="submit" id="modificar"name="modificar">Guardar cambios</button>
        </form>
    </main>
</body>
</html>