<?php
session_start();

// Bloquear acceso si no hay sesión activa
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../index.php");
    exit();
}

require_once "../../assets/sentenciasSQL/usuarios.php";
require_once "../../assets/sentenciasSQL/eventos.php";

$Usuarios = new Usuarios();
$Eventos = new Eventos();
$leerEventos = $Eventos->leerEventos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marcar Asistencia</title>
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
        select {
            margin: 10px auto;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            width: 90%;
            max-width: 400px;
        }
        button {
            padding: 8px 15px;
            border-radius: 8px;
            border: none;
            background-color: #4A7FA7;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #1b7f4d;
        }
    </style>
</head>
<body>
    <button class="regresar" onclick="window.history.back()">Volver</button>
    <h2>Marcar Asistencia</h2>

    <!-- 🔹 SELECT DE EVENTOS -->
    <select name="eventos" id="eventos" onchange="cargarSecciones(this.value)">
        <option value="0">Selecciona un evento</option>
        <?php foreach ($leerEventos as $evento): ?>
            <option value="<?= htmlspecialchars($evento['idE'], ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars($evento['nombre'], ENT_QUOTES, 'UTF-8'); ?> - 
                <?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8'); ?> 
                <?= htmlspecialchars($evento['hora'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- 🔹 SELECT DE SECCIONES (cambia dinámicamente) -->
    <select id="secciones" style="display:none;">
        <option value="0">Selecciona una sección (opcional)</option>
    </select>

    <div class="QR">
        <div id="reader"></div>
        <p id="result">Esperando lectura de QR...</p>

        <p>O escribe el ID del usuario manualmente:</p>
        <div>
            <input type="text" id="manualId" placeholder="Ingresar ID manualmente">
            <button onclick="marcarAsistenciaManual()">Marcar asistencia manual</button>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
    const selectEvento = document.getElementById('eventos');
    const selectSeccion = document.getElementById('secciones');

    // 🔹 Restaurar selección guardada
    const savedEvent = localStorage.getItem('eventoSeleccionado');
    const savedSeccion = localStorage.getItem('seccionSeleccionada');
    if (savedEvent) selectEvento.value = savedEvent;

    // 🔹 Al cambiar evento, guardar y cargar secciones
    selectEvento.addEventListener('change', () => {
        localStorage.setItem('eventoSeleccionado', selectEvento.value);
        cargarSecciones(selectEvento.value);
    });

    // 🔹 Al cambiar sección, guardar
    selectSeccion.addEventListener('change', () => {
        localStorage.setItem('seccionSeleccionada', selectSeccion.value);
    });

    // 🔹 Cargar secciones dinámicamente por evento
    function cargarSecciones(idEvento) {
        if (idEvento === "0") {
            selectSeccion.style.display = "none";
            return;
        }

        fetch('obtener_secciones_evento.php?idE=' + idEvento)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                selectSeccion.innerHTML = '<option value="0">Selecciona una sección</option>';
                data.forEach(sec => {
                    const opt = document.createElement('option');
                    opt.value = sec.idSeccion;
                    opt.textContent = `${sec.nombre_seccion} (${sec.hora_inicio})`;
                    selectSeccion.appendChild(opt);
                });
                selectSeccion.style.display = "block";

                // Restaurar sección seleccionada si coincide con este evento
                const savedSec = localStorage.getItem('seccionSeleccionada');
                if (savedSec) selectSeccion.value = savedSec;
            } else {
                selectSeccion.style.display = "none";
                selectSeccion.innerHTML = "";
                localStorage.removeItem('seccionSeleccionada');
            }
        })
        .catch(err => console.error('Error al cargar secciones:', err));
    }

    // 🔹 Enviar asistencia
    function enviarAsistencia(idR) {
        const idE = selectEvento.value;
        const idS = selectSeccion.value !== "0" ? selectSeccion.value : null;

        if (idE === "0") {
            alert("⚠️ Debes seleccionar un evento antes de marcar asistencia.");
            return;
        }

        fetch('marcar_asistencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr: idR, evento: idE, seccion: idS })
        })
        .then(response => response.text())
        .then(msg => {
            alert(msg);
        })
        .catch(err => {
            console.error('Error al marcar asistencia:', err);
            alert('❌ Ocurrió un error. Intenta de nuevo.');
        });
    }

    // ✅ QR leído correctamente
    function onScanSuccess(decodedText) {
        document.getElementById('result').innerText = "QR leído: " + decodedText;
        enviarAsistencia(decodedText);
    }

    // ✅ Ingreso manual
    function marcarAsistenciaManual() {
        const manualId = document.getElementById('manualId').value.trim();
        if (manualId === "" || isNaN(manualId)) {
            alert("⚠️ Ingresa un ID válido.");
            return;
        }
        enviarAsistencia(manualId);
    }

    // 🔹 Inicializar escáner QR
    new Html5Qrcode("reader").start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        onScanSuccess
    );

    // 🔹 Cargar secciones del evento guardado
    if (savedEvent) cargarSecciones(savedEvent);
    </script>
</body>
</html>
