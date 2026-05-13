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
// CONSULTA
$result = pg_query($conn, "
SELECT 
    id,
    TO_CHAR(
        created_at AT TIME ZONE 'America/Mexico_City',
        'DD/MM/YYYY'
    ) AS fecha,
    TO_CHAR(
        created_at AT TIME ZONE 'America/Mexico_City',
        'HH24:MI'
    ) AS hora,
    solicitante,
    urgencia,
    origen,
    observaciones,
    fecha_manual,
    hora_manual,
    estado,
    observacion_general
FROM registros
ORDER BY created_at DESC
");
$datos = [];
while($row = pg_fetch_assoc($result)){
    $datos[] = $row;
}
echo json_encode($datos);
pg_close($conn);
?>