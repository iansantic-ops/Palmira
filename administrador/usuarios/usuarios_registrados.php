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
        $result = $Usuarios->Leer(); // Refrescar lista
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        /* 🔹 Estilo extra solo para la barra de búsqueda */
        .search-container {
            text-align: center;
            margin: 25px auto;
            width: 90%;
            max-width: 600px;
        }

        .search-container input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            border-color: var(--azul-medio, #4A7FA7);
            box-shadow: 0 0 8px rgba(74, 127, 167, 0.3);
            outline: none;
        }

        @media (max-width: 480px) {
            .search-container input {
                font-size: 0.9rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="../menu_admin.php"><button>Volver al menú</button></a>
        <h2>Usuarios Registrados</h2>
    </header>

    <!-- 🔍 Barra de búsqueda -->
    <div class="search-container">
        <input type="text" id="search" placeholder="Buscar usuario por nombre, correo, país o teléfono...">
    </div>

    <main>
        <table id="usuariosTabla" border="1">
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
                    <td data-label="ID"><?= htmlspecialchars($usuario['idR']); ?></td>
                    <td data-label="Nombre"><?= htmlspecialchars($usuario['nombre']); ?></td>
                    <td data-label="Correo"><?= htmlspecialchars($usuario['correo']); ?></td>
                    <td data-label="Teléfono"><?= htmlspecialchars($usuario['telefono']); ?></td>
                    <td data-label="Medio"><?= htmlspecialchars($usuario['medioE']); ?></td>
                    <td data-label="Origen"><?= htmlspecialchars($usuario['origen']); ?></td>
                    <td data-label="País"><?= htmlspecialchars($usuario['pais']); ?></td>
                    <td>
                        <form method="POST" action="usuarios_registrados.php" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                            <input type="hidden" name="idR" value="<?= htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                        <form action="modificar_usuario.php" method="post">
                            <input type="hidden" name="idR" value="<?= htmlspecialchars($usuario['idR'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" name="modificar">Modificar info</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script>
        // 🔍 Búsqueda en tiempo real
        document.getElementById("search").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase().trim();
            let rows = document.querySelectorAll("#usuariosTabla tbody tr");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>
</body>
</html>
