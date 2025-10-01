<?php
// Recibir datos JSON POST con el idR leído del QR y el idE del evento
$data = json_decode(file_get_contents("php://input"), true);
$idR = $data['qr'] ?? '';
$idE = $data['evento'] ?? 0;

// Validar que sean números válidos
if (!$idR || !ctype_digit($idR) || !$idE || !ctype_digit((string)$idE)) {
    echo "Error: Datos inválidos";
    exit;
}

require_once "../../assets/sentenciasSQL/usuarios.php";
$usuarios = new Usuarios();

// Llamar método que marca asistencia en la tabla inscripciones
$resultado = $usuarios->asistencia($idR, $idE);

if ($resultado) {
    echo "✅ Asistencia registrada correctamente para el evento ";
} else {
    echo "⚠️ Error: No se pudo registrar asistencia (quizá ya esté registrada o no exista inscripción)";
}
