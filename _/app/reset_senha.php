<?php
header("Access-Control-Allow-Origin: *");

include '../classes/clientes.php';
include '../classes/otp_verifications.php';

$c = new clientes();
$otp_verifications = new otp_verifications();

$telefone = $_POST['telefone'];
$senha = $_POST['senha'];
$salt = "anjdsn5s141d5";
$senha = md5($senha . $salt);


$c->resetar_senha_telefone($telefone, $senha);
echo json_encode(array("status" => "sucesso"));

?>