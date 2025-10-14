<?php
include "conexion.php";

class Secciones {
    public function crear_seccion($idE, $nombre, $hora_inicio) {
        include "conexion.php";
        $sql = "INSERT INTO secciones_evento (idE, nombre_seccion, hora_inicio) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idE, $nombre, $hora_inicio]);
    }

    public function obtenerSeccionesPorEvento($idE) {
        include "conexion.php";
        $sql = "SELECT * FROM secciones_evento WHERE idE = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idE]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
