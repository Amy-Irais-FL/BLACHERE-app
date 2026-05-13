<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
include "conexion.php";
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if(!$id){
    echo "ID inválido";
    exit;
}
//eliminar
$result = pg_query_params($conn,
    "DELETE FROM registros WHERE id=$1",
    [$id]
);
if ($result) {
    echo "OK";
} else {
    echo "Error al eliminar";
}
pg_close($conn);
?>