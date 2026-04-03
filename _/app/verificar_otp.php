<?php
header("Access-Control-Allow-Origin: *");
include '../classes/otp_verifications.php';

$otp_verifications = new otp_verifications();

$otp = $_POST['otp'];
$numero_telefone = $_POST['numero_telefone'];
$numero_telefone = str_replace("(", "", $numero_telefone);
$numero_telefone = str_replace(")", "", $numero_telefone);
$numero_telefone = str_replace("-", "", $numero_telefone);
$numero_telefone = str_replace(" ", "", $numero_telefone);
$numero_telefone = "55" . $numero_telefone;

if($otp_verifications->verifica_otp($numero_telefone, $otp)){
    echo json_encode(array('status' => 'ok', 'msg' => 'Código válido'));
}else{
    echo json_encode(array('status' => 'erro', 'msg' => 'Código inválido'));
}

?>