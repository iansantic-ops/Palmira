<?php
include "conexion.php";

class Secciones {
    public function obtenerSeccionesPorEvento($idEvento) {
    include "conexion.php";
    $stmt = $pdo->prepare("SELECT * FROM secciones_evento WHERE idE = :idE ORDER BY hora_inicio ASC");
    $stmt->execute([':idE' => $idEvento]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function crear_seccion($idEvento, $nombre, $horaInicio) {
    include "conexion.php";
    $stmt = $pdo->prepare("INSERT INTO secciones_evento (idE, nombre_seccion, hora_inicio) VALUES (:idE, :nombre, :inicio)");
    return $stmt->execute([
        ':idE' => $idEvento,
        ':nombre' => $nombre,
        ':inicio' => $horaInicio,
        
    ]);
}

public function actualizarSeccion($idSeccion, $nombre, $horaInicio) {
    include "conexion.php";
    $stmt = $pdo->prepare("UPDATE secciones_evento SET nombre_seccion = :nombre, hora_inicio = :inicio WHERE idSeccion = :idSeccion");
    return $stmt->execute([
        ':nombre' => $nombre,
        ':inicio' => $horaInicio,
        ':idSeccion' => $idSeccion
    ]);
}

public function eliminarSeccion($idSeccion) {
    include "conexion.php";
    $stmt = $pdo->prepare("DELETE FROM secciones_evento WHERE idSeccion = :idSeccion");
    return $stmt->execute([':idSeccion' => $idSeccion]);
}

}
?>
