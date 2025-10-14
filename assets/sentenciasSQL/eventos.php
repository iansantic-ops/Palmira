
<?php

class Eventos {

    public function crearEvento($idEvento, $nombre, $descripcion, $fecha, $hora, $lugar, $aforoMax) {
    include "Conexion.php";
    $stmt = $pdo->prepare("
        INSERT INTO eventos (idE, nombre, descripcion, fecha, hora, lugar, aforo_max)
        VALUES (:idEvento, :nombre, :descripcion, :fecha, :hora, :lugar, :aforoMax)
    ");

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
            // Clave duplicada (por idEvento u otra restricción)
            return 'duplicado';
        } else {
            return false; // Otro error
        }
    }
}


   public function leerEventos() {
    try {
        include "conexion.php";
        // 🧩 Ordena por fecha (asc), luego hora (asc) y finalmente nombre (asc)
        $sql = "SELECT * FROM eventos 
                ORDER BY fecha ASC, hora ASC, nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultados ?: []; // ✅ siempre devuelve array
    } catch (PDOException $e) {
        error_log("Error al leer eventos: " . $e->getMessage());
        return []; // ✅ devuelve array vacío en caso de error
    }
}



    public function inscribirUsuario($idE, $idR, $idSeccion = null) {
    try {
        include "conexion.php";

        // Validar si ya está inscrito en esa misma sección
        $sqlCheck = "SELECT * FROM inscripciones WHERE idR = :idR AND idE = :idE AND idSeccion " . 
                    ($idSeccion ? "= :idSeccion" : "IS NULL");
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->bindParam(':idR', $idR);
        $stmt->bindParam(':idE', $idE);
        if ($idSeccion) $stmt->bindParam(':idSeccion', $idSeccion);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return 'duplicado';
        }
        // Insertar inscripción
        $sqlInsert = "INSERT INTO inscripciones (idI, idR, idE, idSeccion, fecha_inscripcion, asistio)
                      VALUES (:idI, :idR, :idE, :idSeccion, NOW(), 0)";
        $stmt = $pdo->prepare($sqlInsert);
        $idI = random_int(10000000, 99999999);
        $stmt->bindParam(':idI', $idI);
        $stmt->bindParam(':idR', $idR);
        $stmt->bindParam(':idE', $idE);
        $stmt->bindParam(':idSeccion', $idSeccion);
        $stmt->execute();

        return 'true';
    } catch (PDOException $e) {
        error_log("Error al inscribir usuario: " . $e->getMessage());
        return 'error';
    }
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
    include "conexion.php";

    $stmt = $pdo->prepare("
        SELECT 
            e.idE,
            e.nombre AS nombre,
            e.fecha AS fecha,
            e.hora AS hora,
            e.lugar AS lugar,
            s.nombre_seccion AS seccion,
            s.hora_inicio,
            i.fecha_inscripcion
        FROM inscripciones i
        JOIN eventos e ON i.idE = e.idE
        LEFT JOIN secciones_evento s ON i.idSeccion = s.idSeccion
        WHERE i.idR = :idUsuario
        ORDER BY e.fecha, s.hora_inicio
    ");
    $stmt->execute([':idUsuario' => $idR]);
    $inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $inscripciones ?: [];
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
public function leerEventosPorSeccion($idSeccion) {
    include "Conexion.php";
    $stmt = $pdo->prepare("
        SELECT * FROM eventos 
        WHERE idSeccion = :idSeccion 
        ORDER BY fecha ASC
    ");
    $stmt->execute([':idSeccion' => $idSeccion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function obtenerSeccionesPorEvento($idE) {
    include "Conexion.php";
    $stmt = $pdo->prepare("SELECT idSeccion, nombre_seccion, hora_inicio FROM secciones_evento WHERE idE = :idE");
    $stmt->execute([':idE' => $idE]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function verInscritosPorSeccion($idE, $idSeccion = null) {
    include "Conexion.php";
    $sql = "
        SELECT r.idR, r.nombre, r.apellidos, r.lada, r.telefono, r.correo, s.nombre_seccion
        FROM inscripciones i
        JOIN registros r ON i.idR = r.idR
        LEFT JOIN secciones_evento s ON i.idSeccion = s.idSeccion
        WHERE i.idE = :idE
    ";
    if ($idSeccion) {
        $sql .= " AND i.idSeccion = :idSeccion";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idE', $idE);
    if ($idSeccion) $stmt->bindParam(':idSeccion', $idSeccion);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function verAsistentesPorSeccion($idE, $idSeccion) {
    include "Conexion.php";
    $stmt = $pdo->prepare("
        SELECT r.idR, r.nombre, r.apellidos, r.lada, r.telefono, r.correo, i.fecha_asistencia
        FROM inscripciones i
        JOIN registros r ON i.idR = r.idR
        WHERE i.idE = :idE AND i.idSeccion = :idSeccion AND i.asistio = 1
    ");
    $stmt->execute([':idE' => $idE, ':idSeccion' => $idSeccion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function estadisticasEventoPorSeccion($idE, $idSeccion) {
    include "Conexion.php";
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total_inscritos,
               SUM(CASE WHEN asistio = 1 THEN 1 ELSE 0 END) AS total_asistentes
        FROM inscripciones
        WHERE idE = :idE AND idSeccion = :idSeccion
    ");
    $stmt->execute([':idE' => $idE, ':idSeccion' => $idSeccion]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



}



?>