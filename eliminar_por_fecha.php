<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
include "conexion.php";
$data = json_decode(file_get_contents("php://input"), true);

$fecha = $data["fecha"] ?? "";
if(!$fecha){
    echo "Fecha vacía";
    exit;
}
//  VALIDAR SI HAY REGISTROS 
$check = pg_query_params($conn,
    "SELECT COUNT(*) as total 
     FROM registros 
     WHERE created_at::date = $1",
    [$fecha]
);

$row = pg_fetch_assoc($check);
if($row["total"] == 0){
    echo "SIN_REGISTROS";
    exit;
}

//  ELIMINAR
$result = pg_query_params($conn,
    "DELETE FROM registros 
     WHERE created_at::date = $1",
    [$fecha]
);

if ($result) {
    echo "OK";
} else {
    echo "Error al eliminar";
}

pg_close($conn);
?>