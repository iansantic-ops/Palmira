<?php
// Leer datos del cuerpo de la solicitud JSON
$data = json_decode(file_get_contents("php://input"), true);

$idR = $data['qr'] ?? '';          // ID del usuario (QR o manual)
$idE = $data['evento'] ?? 0;       // ID del evento
$idS = $data['seccion'] ?? null;   // ID de la sección (opcional)

// Validar que los datos sean válidos
if (!$idR || !ctype_digit((string)$idR) || !$idE || !ctype_digit((string)$idE)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => '⚠️ Error: Datos inválidos']);
    exit;
}

require_once "../../assets/sentenciasSQL/usuarios.php";
$usuarios = new Usuarios();

// Registrar asistencia
$resultado = $usuarios->asistencia($idR, $idE, $idS);

header('Content-Type: application/json; charset=utf-8');
if ($resultado === 'ya_asistio') {
    echo json_encode(['status' => 'warning', 'message' => '⚠️ Este usuario ya registró su asistencia para este evento o sección.']);
} elseif ($resultado === 'no_encontrado') {
    echo json_encode(['status' => 'error', 'message' => '❌ El usuario no está inscrito en este evento o sección.']);
} elseif ($resultado === true) {
    if ($idS) {
        echo json_encode(['status' => 'success', 'message' => '✅ Asistencia registrada correctamente para la sección seleccionada.']);
    } else {
        echo json_encode(['status' => 'success', 'message' => '✅ Asistencia registrada correctamente para el evento general.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '⚠️ Error inesperado al registrar la asistencia.']);
}
