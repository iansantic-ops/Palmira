<?php

class Usuarios{

    public function darAlta($idUsuario, $nombre, $apellidos, $lada, $telefono, $correo, $medioE, $origen, $pais){
        include 'Conexion.php';
        $stmt = $pdo->prepare("INSERT INTO registros (idR, nombre, apellidos, lada, telefono, correo, medioE, origen, pais)
             VALUES(:idUsuario, :nombre, :apellidos, :lada, :telefono, :correo, :medioE, :origen, :pais)"
        );

        try {
            $alta = $stmt->execute([
                ':idUsuario' => $idUsuario,
                ':nombre'    => $nombre,
                ':apellidos' => $apellidos,
                ':lada'      => $lada,
                ':telefono'  => $telefono,
                ':correo'    => $correo,
                ':medioE'    => $medioE,
                ':origen'    => $origen,
                ':pais'      => $pais
            ]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Clave duplicada (correo/teléfono únicos, etc.)
                return 'duplicado';
            }
            return false;
        }
    }

    public function Leer(){
        include 'Conexion.php';
        $stmt = $pdo->prepare("SELECT * FROM registros");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarUsuarioRegistrado($correo, $lada, $telefono){
        include 'Conexion.php';
        $stmt = $pdo->prepare("SELECT idR FROM registros WHERE correo = :correo AND lada = :lada AND telefono = :telefono");
        $stmt->execute([':correo' => $correo, ':lada' => $lada, ':telefono' => $telefono]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
     public function buscarUsuarioPorId($idR) {
        include "Conexion.php"; // $pdo debe estar definido en este archivo
        $stmt = $pdo->prepare("SELECT * FROM registros WHERE idR = :idR");
        $stmt->execute([':idR' => $idR]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para actualizar datos del usuario
    public function modificarUsuario($idR, $nombre, $apellidos, $telefono, $correo, $origen, $pais) {
        include "Conexion.php";
        $stmt = $pdo->prepare("UPDATE registros SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, correo = :correo, origen=:origen, pais=:pais WHERE idR = :idR");
        return $stmt->execute([
            ':nombre'    => $nombre,
            ':apellidos' => $apellidos,
            ':telefono'  => $telefono,
            ':correo'    => $correo,
            ':origen'    => $origen,
            ':pais'      => $pais,
            ':idR'       => $idR
        ]);
    }
    public function eliminarEvento($idR,$idE) {
        include "Conexion.php";
        $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE idR = :idR AND idE = :idE;");
        $eliminar=$stmt->execute([':idR' => $idR, ':idE' => $idE]);
        return $eliminar;
    }
    public function asistencia($idR, $idE, $idS = null) {
    include "conexion.php";

    try {
        // 🔹 Verificar si el usuario está inscrito al evento o sección
        $sqlCheck = "SELECT * FROM inscripciones 
                     WHERE idR = :idR AND idE = :idE " .
                     ($idS ? "AND idSeccion = :idS" : "AND idSeccion IS NULL");

        $stmt = $pdo->prepare($sqlCheck);
        $stmt->bindParam(':idR', $idR);
        $stmt->bindParam(':idE', $idE);
        if ($idS) $stmt->bindParam(':idS', $idS);
        $stmt->execute();

        $inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inscripcion) {
            return 'no_encontrado'; // Usuario no inscrito
        }

        // 🔹 Verificar si ya marcó asistencia
        if ($inscripcion['asistio'] == 1) {
            return 'ya_asistio';
        }

        // 🔹 Marcar asistencia
        $update = $pdo->prepare("
            UPDATE inscripciones 
            SET asistio = 1, fecha_asistencia = NOW()
            WHERE idR = :idR AND idE = :idE " . 
            ($idS ? "AND idSeccion = :idS" : "AND idSeccion IS NULL")
        );

        $update->bindParam(':idR', $idR);
        $update->bindParam(':idE', $idE);
        if ($idS) $update->bindParam(':idS', $idS);

        $update->execute();

        return true;
    } catch (PDOException $e) {
        error_log("Error al registrar asistencia: " . $e->getMessage());
        return false;
    }
}

    public function eliminarUsuario($idR) {
        include "Conexion.php";
        $stmt = $pdo->prepare("DELETE FROM registros WHERE idR = :idR");
        $eliminado=$stmt->execute([':idR' => $idR]);
        return $eliminado;
    }

}   

?>
