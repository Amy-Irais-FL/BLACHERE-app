<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
session_start();
header('Content-Type: application/json');
include "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$usuario = $data["usuario"] ?? "";
$password = $data["password"] ?? "";
/*  USUARIOS2 */
$result = pg_query_params($conn,
    "SELECT * FROM usuarios2 WHERE usuario = $1",
    [$usuario]
);
if($row = pg_fetch_assoc($result)){

    if($password === $row["password"]){

        $_SESSION["usuario"] = $usuario;
        $_SESSION["tipo"] = "usuario2";
        $_SESSION["id"] = $row["id"];

        echo json_encode([
            "estado" => "OK",
            "usuario" => $usuario,
            "tipo" => "usuario2",
            "id" => $row["id"]
        ]);
        exit;
    }
}

/*  ADMIN  */
$result = pg_query_params($conn,
    "SELECT * FROM usuarios WHERE usuario = $1",
    [$usuario]
);

if($row = pg_fetch_assoc($result)){

    if($password === $row["password"]){

        $_SESSION["usuario"] = $usuario;
        $_SESSION["tipo"] = "admin";
        $_SESSION["id"] = $row["id"];

        echo json_encode([
            "estado" => "OK",
            "usuario" => $usuario,
            "tipo" => "admin",
            "id" => $row["id"]
        ]);
        exit;
    }
}

/*  ERROR  */
echo json_encode([
    "estado" => "ERROR"
]);

pg_close($conn);
?>