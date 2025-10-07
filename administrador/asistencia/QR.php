<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/usuarios.php";
$Usuarios = new Usuarios();
$result = $Usuarios->Leer();
require_once "../../assets/sentenciasSQL/eventos.php";
$Eventos = new Eventos();
$leerEventos= $Eventos->leerEventos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leer códigos QR</title>
    <link rel="stylesheet" href="../../assets/css/lista.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
        }
        .QR {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        #result {
            margin-top: 15px;
            font-size: 18px;
            color: #007BFF;
        }
    </style>
</head>
<body>
    <button class="regresar" onclick="window.history.back()">   
        <span>Volver</span>
    </button>
    <h2>Marcar Asistencia</h2>
    <select name="eventos" id="eventos">
        <option value="0">Selecciona un evento</option>
        <?php foreach ($leerEventos as $evento): ?>
            <option value="<?php echo htmlspecialchars($evento['idE'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?> - 
                <?php echo htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?> 
                <?php echo htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
       
    <div class="QR">
        <div id="reader"></div>
        <pre id="result"></pre>
        <!-- Resultado del QR -->
<p id="result">Esperando lectura de QR...</p>

<!-- 🔹 Nuevo input manual para ID del usuario -->
<p>O escribe el ID del usuario manualmente:</p>
<script src="https://unpkg.com/html5-qrcode"></script>
<div>
    <!-- Input manual de ID de usuario -->
    <input type="text" id="manualId" placeholder="Ingresar ID manualmente">
    <button onclick="marcarAsistenciaManual()">Marcar asistencia manual</button>
</div>

<script>
    const select = document.getElementById('eventos');

    // Restaurar selección desde localStorage si existe
    const savedEvent = localStorage.getItem('eventoSeleccionado');
    if (savedEvent) {
        select.value = savedEvent;
    }

    // Guardar selección cada vez que cambie
    select.addEventListener('change', () => {
        localStorage.setItem('eventoSeleccionado', select.value);
    });

    function enviarAsistencia(idR) {
        const idE = select.value;

        // Validar que haya evento seleccionado
        if (idE === "0") {
            alert("⚠️ Debes seleccionar un evento antes de marcar asistencia.");
            return;
        }

        fetch('marcar_asistencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr: idR, evento: idE })
        })
        .then(response => response.text())
        .then(msg => {
            alert(msg);
            // Recargar la página, pero conservar el evento en localStorage
            location.reload();
        })
        .catch(err => {
            console.error('Error al marcar asistencia:', err);
            alert('❌ Ocurrió un error. Intenta de nuevo.');
        });
    }

    // ✅ Caso 1: QR leído correctamente
    function onScanSuccess(decodedText) {
        document.getElementById('result').innerText = decodedText;
        enviarAsistencia(decodedText);
    }

    // ✅ Caso 2: Ingresar ID manual
    function marcarAsistenciaManual() {
        const manualId = document.getElementById('manualId').value.trim();

        if (manualId === "" || isNaN(manualId)) {
            alert("⚠️ Ingresa un ID válido.");
            return;
        }

        enviarAsistencia(manualId);
    }

    // Iniciar escáner QR
    new Html5Qrcode("reader").start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess
    );
</script>
