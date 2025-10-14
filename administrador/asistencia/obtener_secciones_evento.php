<?php
require_once "../../assets/sentenciasSQL/secciones.php";

if (!isset($_GET['idE'])) {
    echo json_encode([]);
    exit();
}

$idE = intval($_GET['idE']);
$Secciones = new Secciones();
$lista = $Secciones->obtenerSeccionesPorEvento($idE);

echo json_encode($lista);
