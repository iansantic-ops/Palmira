
<?php

class Eventos {

    public function crearEvento($idEvento, $nombre, $descripcion, $fecha, $hora, $lugar, $aforoMax, $mapa = null) {
        include "Conexion.php";
        $stmt = $pdo->prepare("
            INSERT INTO eventos (idE, nombre, descripcion, fecha, hora, lugar, aforo_max, mapa)
            VALUES (:idEvento, :nombre, :descripcion, :fecha, :hora, :lugar, :aforoMax, :mapa)
        ");

        try {
            return $stmt->execute([
                ':idEvento'   => $idEvento,
                ':nombre'     => $nombre,
                ':descripcion'=> $descripcion,
                ':fecha'      => $fecha,
                ':hora'       => $hora,
                ':lugar'      => $lugar,
                ':aforoMax'   => $aforoMax,
                ':mapa'       => $mapa
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                return 'duplicado';
            } else {
                return false;
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
        // 1) Obtener fecha y hora del evento/ sección a la que se intenta inscribir
        if ($idSeccion) {
            $stmt = $pdo->prepare("SELECT s.hora_inicio AS hora_reg, e.fecha AS fecha_reg FROM secciones_evento s JOIN eventos e ON s.idE = e.idE WHERE s.idSeccion = :idSeccion LIMIT 1");
            $stmt->execute([':idSeccion' => $idSeccion]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) return 'error';
            $hora_reg = $fila['hora_reg'];
            $fecha_reg = $fila['fecha_reg'];
        } else {
            $stmt = $pdo->prepare("SELECT hora AS hora_reg, fecha AS fecha_reg FROM eventos WHERE idE = :idE LIMIT 1");
            $stmt->execute([':idE' => $idE]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) return 'error';
            $hora_reg = $fila['hora_reg'];
            $fecha_reg = $fila['fecha_reg'];
        }

        // 2) Validar si ya está inscrito (primero):
        //    - Si se intenta inscribir a una sección: comprobar inscripción en la misma sección -> devolver 'duplicado_modulo'
        //    - Si se intenta inscribir al evento general (idSeccion IS NULL): comprobar inscripción general -> devolver 'duplicado_evento'
        $sqlCheck = "SELECT * FROM inscripciones WHERE idR = :idR AND idE = :idE AND idSeccion " . 
                    ($idSeccion ? "= :idSeccion" : "IS NULL");
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->bindParam(':idR', $idR);
        $stmt->bindParam(':idE', $idE);
        if ($idSeccion) $stmt->bindParam(':idSeccion', $idSeccion);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $idSeccion ? 'duplicado_modulo' : 'duplicado_evento';
        }

        // 3) Verificar conflicto de horario SOLO para secciones (submódulos)
        //    Regla: si se intenta inscribir a una sección, bloquear si ya existe
        //    otra inscripción del mismo usuario en cualquier OTRA sección (de cualquier evento)
        //    que ocurra en la misma fecha y misma hora_inicio.
        if ($idSeccion) {
            $sqlConflict = "SELECT i.* FROM inscripciones i 
                            JOIN eventos e ON i.idE = e.idE 
                            JOIN secciones_evento s ON i.idSeccion = s.idSeccion 
                            WHERE i.idR = :idR AND e.fecha = :fecha_reg AND s.hora_inicio = :hora_reg
                              AND (i.idSeccion IS NULL OR i.idSeccion <> :idSeccion)
                            LIMIT 1";
            $stmt = $pdo->prepare($sqlConflict);
            $stmt->execute([':idR' => $idR, ':fecha_reg' => $fecha_reg, ':hora_reg' => $hora_reg, ':idSeccion' => $idSeccion]);
            if ($stmt->rowCount() > 0) {
                return 'conflicto_horario';
            }
        }

        // 4) Insertar inscripción
        $sqlInsert = "INSERT INTO inscripciones (idI, idR, idE, idSeccion, fecha_inscripcion, asistio)
                      VALUES (:idI, :idR, :idE, :idSeccion, NOW(), 0)";
        $stmt = $pdo->prepare($sqlInsert);
        $idI = random_int(10000000, 99999999);
        $stmt->bindParam(':idI', $idI);
        $stmt->bindParam(':idR', $idR);
        $stmt->bindParam(':idE', $idE);
        // Si idSeccion es null, hay que pasar explicitamente null para evitar errores
        if ($idSeccion === null) {
            $stmt->bindValue(':idSeccion', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':idSeccion', $idSeccion);
        }
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
   public function leerEventosUsuario($idUsuario) {
    include "conexion.php";
    $sql = "SELECT 
                e.idE,
                e.nombre,
                e.fecha,
                e.hora,
                e.lugar,
                s.nombre_seccion AS seccion,
                s.hora_inicio AS hora_inicio,
                i.idSeccion AS idSeccion
            FROM inscripciones i
            INNER JOIN eventos e ON i.idE = e.idE
            LEFT JOIN secciones_evento s ON i.idSeccion = s.idSeccion
            WHERE i.idR = :idUsuario";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idUsuario' => $idUsuario]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function leerEventoPorId($idEvento) {
    include "Conexion.php";
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE idE = :idEvento LIMIT 1");
    $stmt->execute([':idEvento' => $idEvento]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function actualizarEvento($idEvento, $nombre, $descripcion, $fecha, $hora, $lugar, $aforoMax, $mapa = null) {
    include "Conexion.php";
    $stmt = $pdo->prepare("UPDATE eventos 
                           SET nombre = :nombre, 
                               descripcion = :descripcion, 
                               fecha = :fecha, 
                               hora = :hora, 
                               lugar = :lugar, 
                               aforo_max = :aforoMax,
                               mapa = :mapa
                           WHERE idE = :idEvento");
    return $stmt->execute([
        ':nombre'      => $nombre,
        ':descripcion' => $descripcion,
        ':fecha'       => $fecha,
        ':hora'        => $hora,
        ':lugar'       => $lugar,
        ':aforoMax'    => $aforoMax,
        ':mapa'        => $mapa,
        ':idEvento'    => $idEvento
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


    /**
     * Elimina todos los eventos cuya fecha sea anterior a hoy.
     * Borra también inscripciones y secciones asociadas dentro de una transacción.
     * Devuelve true si se borraron (o no había nada que borrar), false en error, o 0 si no había eventos pasados.
     */
    public function eliminarEventosPasados() {
        include "Conexion.php";
        try {
            $hoy = date('Y-m-d');
            // Obtener ids de eventos pasados
            $stmt = $pdo->prepare("SELECT idE FROM eventos WHERE fecha < :hoy");
            $stmt->execute([':hoy' => $hoy]);
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($ids)) {
                return 0; // nada que eliminar
            }

            // Preparar placeholders
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $pdo->beginTransaction();

            // Eliminar inscripciones relacionadas
            $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE idE IN ($placeholders)");
            $stmt->execute($ids);

            // Eliminar secciones relacionadas
            $stmt = $pdo->prepare("DELETE FROM secciones_evento WHERE idE IN ($placeholders)");
            $stmt->execute($ids);

            // Eliminar eventos
            $stmt = $pdo->prepare("DELETE FROM eventos WHERE idE IN ($placeholders)");
            $stmt->execute($ids);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Error al eliminar eventos pasados: ' . $e->getMessage());
            return false;
        }
    }



}



?>