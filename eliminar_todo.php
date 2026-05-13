<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
include "conexion.php";

// eliminar TODOS los registros
$result = pg_query($conn, "DELETE FROM registros");

if ($result) {
    echo "OK";
} else {
    echo "Error al eliminar";
}

pg_close($conn);
?>