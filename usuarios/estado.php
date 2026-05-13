<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
include "../conexion.php";
$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;
$estado = $data["estado"] ?? "";
$fecha_manual = $data["fecha_manual"] ?? null;
$hora_manual = $data["hora_manual"] ?? null;

if(!$id || !$estado){
    echo "DATOS_INVALIDOS";
    exit;
}

$result = pg_query_params($conn, "
    UPDATE registros
    SET estado = $1,
        fecha_manual = $2,
        hora_manual = $3
    WHERE id = $4
", [
    $estado,
    $fecha_manual,
    $hora_manual,
    $id
]);

if($result){
    echo "OK";
}else{
    echo "ERROR";
}

pg_close($conn);
?>