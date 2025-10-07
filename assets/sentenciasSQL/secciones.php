<?php

class Secciones {

    // 🟢 Crear una nueva sección
    public function crearSeccion($nombre, $descripcion) {
        include "Conexion.php"; // obtiene $pdo desde tu conexión
        $stmt = $pdo->prepare("INSERT INTO secciones (nombre_seccion, descripcion) VALUES (:nombre, :descripcion)");

        try {
            $ok = $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion
            ]);
            return $ok;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                // clave duplicada (por nombre único, si lo tienes)
                return 'duplicado';
            } else {
                return false;
            }
        }
    }

    // 🟡 Leer todas las secciones
    public function obtenerSecciones() {
        include "Conexion.php";
        $stmt = $pdo->prepare("SELECT * FROM secciones ORDER BY idSeccion ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔵 Leer una sección específica
    public function obtenerSeccionPorId($idSeccion) {
        include "Conexion.php";
        $stmt = $pdo->prepare("SELECT * FROM secciones WHERE idSeccion = :idSeccion LIMIT 1");
        $stmt->execute([':idSeccion' => $idSeccion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🟣 Actualizar una sección
    public function actualizarSeccion($idSeccion, $nombre, $descripcion) {
        include "Conexion.php";
        $stmt = $pdo->prepare("UPDATE secciones 
                               SET nombre_seccion = :nombre, 
                                   descripcion = :descripcion
                               WHERE idSeccion = :idSeccion");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':idSeccion' => $idSeccion
        ]);
    }

    // 🔴 Eliminar solo una sección (sin tocar eventos)
    public function eliminarSeccion($idSeccion) {
        include "Conexion.php";
        $stmt = $pdo->prepare("DELETE FROM secciones WHERE idSeccion = :idSeccion");
        return $stmt->execute([':idSeccion' => $idSeccion]);
    }

    // ⚫ Eliminar sección y todos sus eventos
    public function eliminarSeccionYEventos($idSeccion) {
        include "Conexion.php";
        try {
            $pdo->beginTransaction();

            // Primero eliminar los eventos de esa sección
            $stmtEventos = $pdo->prepare("DELETE FROM eventos WHERE idSeccion = :idSeccion");
            $stmtEventos->execute([':idSeccion' => $idSeccion]);

            // Luego eliminar la sección
            $stmtSeccion = $pdo->prepare("DELETE FROM secciones WHERE idSeccion = :idSeccion");
            $stmtSeccion->execute([':idSeccion' => $idSeccion]);

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
?>
