<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

date_default_timezone_set('America/Mexico_City');

include "../conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

if(!$id){
    echo "ID_INVALIDO";
    exit;
}

// ===== BUSCAR REGISTRO =====
$res = pg_query_params($conn,
    "SELECT *,
        TO_CHAR(created_at AT TIME ZONE 'America/Mexico_City','DD/MM/YYYY') AS fecha,
        TO_CHAR(created_at AT TIME ZONE 'America/Mexico_City','HH24:MI') AS hora
     FROM registros
     WHERE id = $1",
    [$id]
);

if($row = pg_fetch_assoc($res)){

    $solicitante = $row["solicitante"];
    $urgencia = $row["urgencia"];
    $origen = $row["origen"];
    $observaciones = $row["observaciones"];

    $mail = new PHPMailer(true);

    try {

        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'error_log';
        $mail->Timeout = 30;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amyfernandezlargo@gmail.com';
        $mail->Password = 'zxvholbypvozdzsi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('amyfernandezlargo@gmail.com', 'Sistema Solicitudes');
        $mail->addAddress('amyfernandezlargo@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'Solicitud NO realizada';

        $mail->Body = "
            <h3>⚠️ Solicitud pendiente</h3>
            <p>La siguiente solicitud NO fue realizada:</p>
            <b>Solicitante:</b> {$solicitante}<br>
            <b>Urgencia:</b> {$urgencia}<br>
            <b>Tema:</b> {$origen}<br>
            <b>Observaciones:</b> {$observaciones}<br>
            <b>Fecha original:</b> {$row["fecha"]}<br>
            <b>Hora original:</b> {$row["hora"]}
        ";

        if($mail->send()){
            error_log("Correo enviado");
        }else{
            error_log("No enviado");
        }

    } catch (Exception $e) {
        error_log("ERROR_MAIL" . $mail->ErrorInfo);
    }

} else {
    echo "NO_ENCONTRADO";
}

pg_close($conn);
?>