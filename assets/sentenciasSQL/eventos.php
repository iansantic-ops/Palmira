
<?php

class Eventos {

    public function crearEvento($idEvento, $nombre, $descripcion, $fecha, $hora, $lugar, $aforoMax) {
        include "Conexion.php";
        $stmt = $pdo->prepare("INSERT INTO eventos (idE, nombre, descripcion, fecha, hora, lugar, aforo_max) 
                               VALUES (:idEvento, :nombre, :descripcion, :fecha, :hora, :lugar, :aforoMax)");
        try {
            $alta = $stmt->execute([
                ':idEvento'   => $idEvento,
                ':nombre'     => $nombre,
                ':descripcion'=> $descripcion,
                ':fecha'      => $fecha,
                ':hora'       => $hora,
                ':lugar'      => $lugar,
                ':aforoMax'   => $aforoMax
            ]);
            return $alta;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                // clave duplicada
                return 'duplicado';
            } else {
                return false; // Otro error
            }
        }
    }

    public function leerEventos() {
        include "Conexion.php";
        $stmt = $pdo->prepare("SELECT * FROM eventos ORDER BY fecha ASC");
        $stmt->execute();
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $eventos;
    }

    public function inscribirUsuario($idEvento, $idUsuario) {
        include "Conexion.php";

        // Primero checamos si ya está inscrito
        $stmt = $pdo->prepare("SELECT * FROM inscripciones WHERE idE = :idEvento AND idR = :idUsuario");
        $stmt->execute([':idEvento'=>$idEvento, ':idUsuario'=>$idUsuario]);
        $yaExiste = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($yaExiste) {
            return 'duplicado';
        }

        // Insertamos inscripción
        $stmt = $pdo->prepare("INSERT INTO inscripciones (idE, idR) VALUES (:idEvento, :idUsuario)");
        $ok = $stmt->execute([':idEvento'=>$idEvento, ':idUsuario'=>$idUsuario]);

        return $ok ? 'true' : 'false';
    }

   public function verInscritos($idEvento) {
        include "Conexion.php";
        $stmt = $pdo->prepare("
            SELECT r.idR, r.nombre, r.apellidos, r.lada, r.telefono, r.correo, r.medioE, r.origen, r.pais
            FROM inscripciones i
            JOIN registros r ON i.idR = r.idR
            WHERE i.idE = :idEvento
        ");
        $stmt->execute([':idEvento' => $idEvento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function leerEventosUsuario($idR) {
    include "Conexion.php"; // Incluye el PDO dentro del método
    $stmt = $pdo->prepare("
        SELECT e.*
        FROM eventos e
        INNER JOIN inscripciones i ON e.idE = i.idE
        WHERE i.idR = :idR
        ORDER BY e.fecha ASC
    ");
    $stmt->execute([':idR' => $idR]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function leerEventoPorId($idEvento) {
    include "Conexion.php";
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE idE = :idEvento LIMIT 1");
    $stmt->execute([':idEvento' => $idEvento]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function actualizarEvento($idEvento, $nombre, $descripcion, $fecha, $hora, $lugar, $aforoMax) {
    include "Conexion.php";
    $stmt = $pdo->prepare("UPDATE eventos 
                           SET nombre = :nombre, 
                               descripcion = :descripcion, 
                               fecha = :fecha, 
                               hora = :hora, 
                               lugar = :lugar, 
                               aforo_max = :aforoMax
                           WHERE idE = :idEvento");
    return $stmt->execute([
        ':nombre'     => $nombre,
        ':descripcion'=> $descripcion,
        ':fecha'      => $fecha,
        ':hora'       => $hora,
        ':lugar'      => $lugar,
        ':aforoMax'   => $aforoMax,
        ':idEvento'   => $idEvento
    ]);
}

 public function eliminarEvento($idEvento) {
    include "Conexion.php";
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE idE = :idEvento");
    return $stmt->execute([':idEvento' => $idEvento]);
}
public function verAsistentes($idEvento){
    include "Conexion.php";
    $stmt = $pdo->prepare("
        SELECT r.idR, r.nombre, r.apellidos, r.lada, r.telefono, r.correo,i.fecha_asistencia
            FROM inscripciones i
            JOIN registros r ON i.idR = r.idR
            WHERE i.idE = :idEvento AND i.asistio = 1
    ");
    $stmt->execute([':idEvento' => $idEvento]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function estadisticasEvento($idEvento){
    include "Conexion.php";
    $stmt=$pdo->prepare("SELECT COUNT(*) AS total_inscritos, 
    SUM(CASE WHEN asistio = 1 THEN 1 ELSE 0 END) AS total_asistentes
    FROM inscripciones
    WHERE idE = :idEvento");
    $stmt->execute([':idEvento' => $idEvento]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}



?>