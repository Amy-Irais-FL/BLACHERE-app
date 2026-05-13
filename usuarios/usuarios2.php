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
if (!$conn) {
    echo json_encode(["error" => "conexion"]);
    exit;
}
// ===== OBTENER DATOS =====
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $data = [
        "accion" => "listar",
        "busqueda" => ""
    ];
} else {
    $data = json_decode(file_get_contents("php://input"), true);
}

if(!$data || !isset($data["accion"])){
    echo json_encode(["error" => "SIN_DATOS"]);
    exit;
}
$accion = $data["accion"];
// ================= LOGIN =================
if($accion == "login"){
    $usuario = $data["usuario"] ?? "";
    $password = $data["password"] ?? "";

    $res = pg_query_params($conn,
        "SELECT * FROM usuarios2 WHERE usuario=$1",
        [$usuario]
    );

    if($row = pg_fetch_assoc($res)){
        if($password === $row["password"]){
            $_SESSION["usuario"] = $usuario;
            $_SESSION["tipo"] = "usuario2";

            echo json_encode([
                "estado" => "OK",
                "usuario" => $usuario,
                "tipo" => "usuario2"
            ]);
        }else{
            echo json_encode(["estado"=>"ERROR"]);
        }
    }else{
        echo json_encode(["estado"=>"ERROR"]);
    }
}
// ================= REGISTRO =================
elseif($accion == "registro"){
    $usuario = $data["usuario"] ?? "";
    $password = $data["password"] ?? "";

    if(!$usuario || !$password){
        echo json_encode(["error"=>"Campos vacíos"]);
        exit;
    }
    // verificar si existe
    $check = pg_query_params($conn,
        "SELECT id FROM usuarios2 WHERE usuario=$1",
        [$usuario]
    );
    if(pg_num_rows($check) > 0){
        echo json_encode(["error"=>"Usuario ya existe"]);
        exit;
    }

    $res = pg_query_params($conn,
        "INSERT INTO usuarios2 (usuario, password) VALUES ($1,$2)",
        [$usuario, $password]
    );
    echo json_encode($res ? ["estado"=>"OK"] : ["estado"=>"ERROR"]);
}
// ================= LISTAR =================
elseif($accion == "listar"){
    $busqueda = $data["busqueda"] ?? "";
    $like = "%$busqueda%";
    $res = pg_query_params($conn,
        "SELECT id, usuario, password FROM usuarios2 WHERE usuario ILIKE $1 ORDER BY id ASC",
        [$like]
    );

    $usuarios = [];
    while($row = pg_fetch_assoc($res)){
        $usuarios[] = $row;
    }
    echo json_encode($usuarios);
}
// ================= EDITAR =================
elseif($accion == "editar"){
    $id = $data["id"] ?? 0;
    $usuario = $data["usuario"] ?? "";

    if(!$id || !$usuario){
        echo json_encode(["error"=>"Datos inválidos"]);
        exit;
    }

    $res = pg_query_params($conn,
        "UPDATE usuarios2 SET usuario=$1 WHERE id=$2",
        [$usuario, $id]
    );
    echo json_encode($res ? ["estado"=>"OK"] : ["estado"=>"ERROR"]);
}
// ================= ELIMINAR =================
elseif($accion == "eliminar"){
    $id = $data["id"] ?? 0;

    if(!$id){
        echo json_encode(["error"=>"ID inválido"]);
        exit;
    }
    $res = pg_query_params($conn,
        "DELETE FROM usuarios2 WHERE id=$1",
        [$id]
    );

    echo json_encode($res ? ["estado"=>"OK"] : ["estado"=>"ERROR"]);
}
// ================= DEFAULT =================
else{
    echo json_encode(["error"=>"ACCION_INVALIDA"]);
}

pg_close($conn);
?>