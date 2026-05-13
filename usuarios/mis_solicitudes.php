<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
include "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data["usuario"] ?? "";

if(!$usuario){
    echo json_encode([]);
    exit;
}

// del solicitante que NO estén terminados
$result = pg_query_params($conn, "
    SELECT *,
        created_at::date AS fecha,
        created_at::time AS hora
    FROM registros
    WHERE solicitante ILIKE $1
     AND estado != 'Terminado'
     ORDER BY id DESC
", [$usuario]);

$datos = [];

while($row = pg_fetch_assoc($result)){
    $datos[] = $row;
}

echo json_encode($datos);

pg_close($conn);
?>