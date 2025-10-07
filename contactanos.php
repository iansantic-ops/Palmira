
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contáctanos</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .contact-form {
            max-width: 400px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .contact-form label { display: block; margin-top: 10px; }
        .contact-form input, .contact-form textarea {
            width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;
        }
        .contact-form button {
            margin-top: 15px; padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px;
            cursor: pointer;
        }
        .contact-form button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="contact-form">
        <h2>Contáctanos</h2>
        <form action="contactanos.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="4" required></textarea>

            <button type="submit">Enviar</button>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = htmlspecialchars($_POST["nombre"]);
            $email = htmlspecialchars($_POST["email"]);
            $mensaje = htmlspecialchars($_POST["mensaje"]);
            // Aquí puedes agregar el código para enviar el correo o guardar el mensaje
            echo "<p>¡Gracias por contactarnos, $nombre! Pronto te responderemos.</p>";
        }
        ?>
    </div>
</body>
</html>