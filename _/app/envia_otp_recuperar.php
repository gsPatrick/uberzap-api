<?php
header("Access-Control-Allow-Origin: *");
include '../classes/otp_verifications.php';
include '../classes/clientes.php';
include '../bd/config.php';


$otp_verifications = new otp_verifications();
$c = new clientes();


$numero_telefone = $_POST['numero_telefone'];
$numero_telefone = str_replace("(", "", $numero_telefone);
$numero_telefone = str_replace(")", "", $numero_telefone);
$numero_telefone = str_replace("-", "", $numero_telefone);
$numero_telefone = str_replace(" ", "", $numero_telefone);

//verifica se o numero ja esta cadastrado
if (!$c->get_cliente_telefone($numero_telefone) != false) {
    echo json_encode(array('status' => 'erro', 'msg' => 'Você não está cadastrado! Faça seu cadastro!'));
    exit;
}

$otp = $otp_verifications->insere("55" . $numero_telefone);

$numero_telefone = "55" . $numero_telefone;


$msg = "Seu código de verificação é: " . $otp;
$api_key = API_KEY_SMS;
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.smsdev.com.br/v1/send?key=" . $api_key . "&type=9&number=" . $numero_telefone . "&msg=" . urlencode($msg),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
echo json_encode(array('status' => 'ok'));

?>
