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

$usuario = trim($data["usuario"]);
$password = trim($data["password"]);

if($usuario == "" || $password == ""){
    echo "Campos vacíos";
    exit;
}

// verificar si ya existe
$check = pg_query_params($conn,
    "SELECT id FROM usuarios2 WHERE usuario=$1",
    [$usuario]
);

if(pg_num_rows($check) > 0){
    echo "Usuario ya existe";
    exit;
}

// insertar
$result = pg_query_params($conn,
    "INSERT INTO usuarios2 (usuario, password) VALUES ($1, $2)",
    [$usuario, $hash]
);

if($result){
    echo "OK";
}else{
    echo "Error al registrar";
}

pg_close($conn);
?>