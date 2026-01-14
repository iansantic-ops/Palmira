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
$allEventos = $Eventos->leerEventos();
// Mostrar sólo eventos próximos (fecha >= hoy)
$hoy = date('Y-m-d');
$leerEventos = array_values(array_filter($allEventos, function($ev) use ($hoy) {
    return isset($ev['fecha']) && $ev['fecha'] >= $hoy;
}));
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
        /* Overlay para marcar donde colocar el QR */
        #reader {
            position: relative;
            margin: 0 auto;
            max-width: 500px;
            height: 350px;
            background: #000; /* mientras no hay cámara */
            border-radius: 12px;
            overflow: visible;
        }

        .qr-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 62%;
            height: 48%;
            transform: translate(-50%, -50%);
            border-radius: 12px;
            /* sombrear todo el fondo excepto el rectángulo central */
            box-shadow: 0 0 0 2000px rgba(0,0,0,0.62);
            pointer-events: none;
            overflow: visible;
        }

        /* Marcas blancas sólo en las esquinas del rectángulo */
        .qr-overlay .corner {
            position: absolute;
            width: 44px;
            height: 44px;
            box-sizing: border-box;
        }

        .qr-overlay .top-left {
            top: -2px;
            left: -2px;
            border-top: 4px solid #fff;
            border-left: 4px solid #fff;
            border-top-left-radius: 6px;
        }

        .qr-overlay .top-right {
            top: -2px;
            right: -2px;
            border-top: 4px solid #fff;
            border-right: 4px solid #fff;
            border-top-right-radius: 6px;
        }

        .qr-overlay .bottom-left {
            bottom: -2px;
            left: -2px;
            border-bottom: 4px solid #fff;
            border-left: 4px solid #fff;
            border-bottom-left-radius: 6px;
        }

        .qr-overlay .bottom-right {
            bottom: -2px;
            right: -2px;
            border-bottom: 4px solid #fff;
            border-right: 4px solid #fff;
            border-bottom-right-radius: 6px;
        }

        .qr-overlay .center-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 8px;
            height: 8px;
            background: rgba(255,255,255,0.95);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 8px rgba(255,255,255,0.08);
        }

        #result {
            margin-top: 15px;
            font-size: 18px;
            color: #007BFF;
            min-height: 24px;
        }

        .msg-success { color: #1b7f4d; }
        .msg-error { color: #c62828; }
        .msg-warn { color: #b58b00; }
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
        <div id="reader">
            <div class="qr-overlay" aria-hidden="true">
                <div class="corner top-left"></div>
                <div class="corner top-right"></div>
                <div class="corner bottom-left"></div>
                <div class="corner bottom-right"></div>
                <div class="center-dot"></div>
            </div>
        </div>
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

    // 🔹 Enviar asistencia (muestra mensajes en pantalla en lugar de alert)
    let lastScanned = null;
    let scanCooldown = 2000; // ms
    let lastScanTime = 0;

    function showMessage(text, status='info'){
        const el = document.getElementById('result');
        el.textContent = text;
        el.classList.remove('msg-success','msg-error','msg-warn');
        if(status === 'success') el.classList.add('msg-success');
        if(status === 'error') el.classList.add('msg-error');
        if(status === 'warning') el.classList.add('msg-warn');
    }

    function enviarAsistencia(idR) {
        const idE = selectEvento.value;
        const idS = selectSeccion.value !== "0" ? selectSeccion.value : null;

        if (idE === "0") {
            showMessage("⚠️ Debes seleccionar un evento antes de marcar asistencia.", 'warning');
            return;
        }

        // evitar reenvío inmediato del mismo id
        const now = Date.now();
        if (lastScanned === idR && (now - lastScanTime) < scanCooldown) {
            // ignorar duplicado dentro del cooldown
            return;
        }

        fetch('marcar_asistencia.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr: idR, evento: idE, seccion: idS })
        })
        .then(response => response.json())
        .then(data => {
            if (!data || !data.status) {
                showMessage('❌ Respuesta inesperada del servidor', 'error');
                return;
            }
            if (data.status === 'success') {
                showMessage(data.message || '✅ Asistencia registrada', 'success');
            } else if (data.status === 'warning') {
                showMessage(data.message || '⚠️ Atención', 'warning');
            } else {
                showMessage(data.message || '❌ Error', 'error');
            }
            lastScanned = idR;
            lastScanTime = Date.now();
            // pausar el escáner unos segundos para que el operario pueda leer el mensaje
            pauseScanner(1500);
        })
        .catch(err => {
            console.error('Error al marcar asistencia:', err);
            showMessage('❌ Ocurrió un error. Intenta de nuevo.', 'error');
        });
    }

    // ✅ QR leído correctamente
    function onScanSuccess(decodedText) {
        // mostrar lectura y procesar
        document.getElementById('result').innerText = "QR leído: " + decodedText;
        enviarAsistencia(decodedText);
    }

    // ✅ Ingreso manual
    function marcarAsistenciaManual() {
        const manualId = document.getElementById('manualId').value.trim();
        if (manualId === "" || isNaN(manualId)) {
            showMessage("⚠️ Ingresa un ID válido.", 'warning');
            return;
        }
        enviarAsistencia(manualId);
    }

    // 🔹 Inicializar escáner QR
    let html5Qr = new Html5Qrcode("reader");
    let scanningActive = false;

    function startScanner() {
        html5Qr.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).then(() => {
            scanningActive = true;
        }).catch(err => {
            console.error('No se pudo iniciar la cámara:', err);
            showMessage('❌ No se pudo iniciar la cámara. Comprueba permisos y cámara.', 'error');
            scanningActive = false;
        });
    }

    function pauseScanner(ms) {
        if (!html5Qr || !scanningActive) return Promise.resolve();
        scanningActive = false;
        return html5Qr.stop().then(() => {
            // esperar ms milisegundos y reanudar
            return new Promise((resolve) => {
                setTimeout(() => {
                    // reanudar
                    html5Qr.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess)
                    .then(() => {
                        scanningActive = true;
                        resolve();
                    })
                    .catch(err => {
                        console.error('Error reiniciando cámara:', err);
                        showMessage('❌ No se pudo reanudar la cámara. Comprueba permisos y cámara.', 'error');
                        resolve();
                    });
                }, ms);
            });
        }).catch(err => {
            console.error('Error al detener la cámara:', err);
            return Promise.resolve();
        });
    }

    // iniciar al cargar
    startScanner();

    // 🔹 Cargar secciones del evento guardado
    if (savedEvent) cargarSecciones(savedEvent);
    </script>
</body>
</html>
