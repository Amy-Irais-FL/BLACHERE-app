<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conexion.php";
$data = json_decode(file_get_contents("php://input"), true);

$solicitante = $data['solicitante'];
$urgencia = $data['urgencia'];
$origen = $data['origen'];
$observaciones = $data['observaciones'];
$fecha_manual = $data['fecha_manual'];
$hora_manual = $data['hora_manual'];
$estado = $data['estado'];
$observacion_general = $data['observacion_general'];
$id = $data['id'];

$query = "UPDATE registros SET
    solicitante=$1,
    urgencia=$2,
    origen=$3,
    observaciones=$4,
    fecha_manual=$5,
    hora_manual=$6,
    estado=$7,
    observacion_general=$8
WHERE id=$9";

$result = pg_query_params($conn, $query, [
    $solicitante,
    $urgencia,
    $origen,
    $observaciones,
    $fecha_manual,
    $hora_manual,
    $estado,
    $observacion_general,
    $id
]);

if ($result) {
    echo "OK";
} else {
    echo "Error al actualizar";
}
?>