<?php
// Leer datos del cuerpo de la solicitud JSON
$data = json_decode(file_get_contents("php://input"), true);

$idR = $data['qr'] ?? '';          // ID del usuario (QR o manual)
$idE = $data['evento'] ?? 0;       // ID del evento
$idS = $data['seccion'] ?? null;   // ID de la sección (opcional)

// Validar que los datos sean válidos
if (!$idR || !ctype_digit((string)$idR) || !$idE || !ctype_digit((string)$idE)) {
    echo "⚠️ Error: Datos inválidos";
    exit;
}

require_once "../../assets/sentenciasSQL/usuarios.php";
$usuarios = new Usuarios();

// Registrar asistencia
$resultado = $usuarios->asistencia($idR, $idE, $idS);

if ($resultado === 'ya_asistio') {
    echo "⚠️ Este usuario ya registró su asistencia para este evento o sección.";
} elseif ($resultado === 'no_encontrado') {
    echo "❌ El usuario no está inscrito en este evento o sección.";
} elseif ($resultado === true) {
    if ($idS) {
        echo "✅ Asistencia registrada correctamente para la sección seleccionada.";
    } else {
        echo "✅ Asistencia registrada correctamente para el evento general.";
    }
} else {
    echo "⚠️ Error inesperado al registrar la asistencia.";
}
