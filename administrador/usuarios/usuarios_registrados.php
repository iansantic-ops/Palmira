<?php
include_once ("../../assets/sentenciasSQL/usuarios.php");
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}
$Usuarios = new Usuarios();
$result = $Usuarios->Leer();
if (isset($_POST['eliminar']) && isset($_POST['idR'])) {
    $idR = intval($_POST['idR']);
    $eliminado = $Usuarios->eliminarUsuario($idR);
    if ($eliminado) {
        // Refrescar la lista de usuarios
        $result = $Usuarios->Leer();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios en plataforma</title>
    <link rel="stylesheet" href="../../assets/css/usuarios.css">

</head>
<body>
    <header>
        <a href="../menu_admin.php"><button>Volver al menu</button></a>
        <h2>Usuarios Registrados</h2>
    </header>
    <main>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Medio</th>
                    <th>Origen</th>
                    <th>País</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $usuario): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($usuario['idR']); ?></td>
                    <td data-label="Nombre"><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td data-label="Correo"><?php echo htmlspecialchars($usuario['correo']); ?></td>
                    <td data-label="Teléfono"><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                    <td data-label="Medio"><?php echo htmlspecialchars($usuario['medioE']); ?></td>
                    <td data-label="Origen"><?php echo htmlspecialchars($usuario['origen']); ?></td>
                    <td data-label="País"><?php echo htmlspecialchars($usuario['pais']); ?></td>
   
                    <td>
                        <form method="POST" action="usuarios_registrados.php" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                            <input type="hidden" name="idR" value="<?php echo htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                        <form action="modificar_usuario.php" method="post">
                            <input type="hidden" name="idR" value="<?php echo htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="modificar">Modificar info</button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>