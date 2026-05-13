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

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

date_default_timezone_set('America/Mexico_City');
include "conexion.php";
$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo "No llegaron datos";
    exit;
}

// ===== DATOS =====
$solicitante = $data['solicitante'] ?? "";
$urgencia = $data['urgencia'] ?? "";
$origen = $data['origen'] ?? "";
$observaciones = $data['observaciones'] ?? "";

// ===== CAPITALIZAR =====
function capitalizar($texto){
    return ucfirst(mb_strtolower($texto, "UTF-8"));
}
$solicitante = capitalizar($solicitante);
$observaciones = capitalizar($observaciones);

// ===== VALORES DEFAULT =====
$observacion_general = "";
$fecha_manual = NULL;
$hora_manual = NULL;
$estado = "Sin empezar";

// ===== VALIDAR DUPLICADO =====
$check = pg_query_params($conn,
    "SELECT COUNT(*) as total 
     FROM registros 
     WHERE solicitante = $1
     AND created_at >= NOW() - INTERVAL '1 minute'",
    [$solicitante]
);
$row = pg_fetch_assoc($check);
if($row["total"] > 0){
    echo "DUPLICADO";
    exit;
}

// ===== INSERT =====
$result = pg_query_params($conn,
    "INSERT INTO registros 
    (solicitante, urgencia, origen, observaciones, observacion_general, fecha_manual, hora_manual, estado)
    VALUES ($1,$2,$3,$4,$5,$6,$7,$8)",
    [
        $solicitante,
        $urgencia,
        $origen,
        $observaciones,
        $observacion_general,
        $fecha_manual,
        $hora_manual,
        $estado
    ]
);
// ===== SI GUARDÓ =====
if ($result) {
    echo "OK";
    // ===== CORREO =====
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
        $mail->Subject = 'Nueva solicitud';
        $mail->Body = "
            <h3>📢 Nueva solicitud</h3>
            <b>Solicitante:</b> $solicitante <br>
            <b>Urgencia:</b> $urgencia <br>
            <b>Tema:</b> $origen <br>
            <b>Observaciones:</b> $observaciones <br>
            <b>Fecha:</b> ".date("d/m/Y H:i")."
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
    echo "Error BD";
}
pg_close($conn);
?>