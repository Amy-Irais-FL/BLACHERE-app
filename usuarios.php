<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

session_start();
header('Content-Type: application/json');
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["estado"=>"SIN_DATOS"]);
    exit;
}

/* LOGIN */
if (($data["accion"] ?? "") == "login") {
    $usuario = $data["usuario"] ?? "";
    $passwordPlano = $data["password"] ?? "";

    $res = pg_query_params(
        $conn,
        "SELECT * FROM usuarios WHERE usuario=$1",
        [$usuario]
    );

    if ($res && pg_num_rows($res) > 0) {
        $row = pg_fetch_assoc($res);

        if ($passwordPlano === $row["password"]) {
            $_SESSION["usuario"] = $usuario;
            $_SESSION["tipo"] = "admin";

            echo json_encode([
                "estado" => "OK",
                "usuario" => $usuario,
                "tipo" => "admin"
            ]);
        } else {
            echo json_encode(["estado"=>"ERROR"]);
        }
    } else {
        echo json_encode(["estado"=>"ERROR"]);
    }
}

/* OBTENER SESIÓN */
if (($data["accion"] ?? "") == "obtenerSesion") {
    if (isset($_SESSION["usuario"])) {
        $usuario = $_SESSION["usuario"];

        $res = pg_query($conn, "SELECT password FROM usuarios WHERE id=2");
        $row = pg_fetch_assoc($res);

        echo json_encode([
            "estado" => "OK",
            "usuario" => $usuario,
            "password" => $row["password"]
        ]);
    } else {
        echo json_encode(["estado"=>"ERROR"]);
    }
}

/* ACTUALIZAR */
if (($data["accion"] ?? "") == "actualizar") {
    if (!isset($_SESSION["usuario"])) {
        echo "NO_SESSION";
        exit;
    }

    $nuevo = $data["usuarioNuevo"] ?? "";
    $passwordPlano = $data["password"] ?? "";

    if (empty($nuevo)) {
        echo "USUARIO_VACIO";
        exit;
    }

    if (!empty($passwordPlano)) {
        $res = pg_query_params(
            $conn,
            "UPDATE usuarios SET usuario=$1, password=$2 WHERE id=$3",
            [$nuevo, $passwordPlano, 2]
        );
    } else {
        $res = pg_query_params(
            $conn,
            "UPDATE usuarios SET usuario=$1 WHERE id=$2",
            [$nuevo, 2]
        );
    }

    if ($res) {
        $_SESSION["usuario"] = $nuevo;
        $_SESSION["id"] = 2;
        echo "OK";
    } else {
        echo "ERROR";
    }
}

pg_close($conn);
?>