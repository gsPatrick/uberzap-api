<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
$clientes = new Clientes();

$cidade_id = $_POST['cidade_id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];
$cidade_id = $_POST['cidade_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$salt = "anjdsn5s141d5";
$senha = md5($senha.$salt);

//verifica se o telefone já existe
$verifica = $clientes->verifica_se_existe($telefone);
if($verifica){
    echo json_encode(array("status" => "Telefone já cadastrado"));
    exit;
}

//cadastra o cliente
$cadastra = $clientes->cadastra($cidade_id, $nome, $email, $telefone, $senha, $latitude, $longitude);
if($cadastra){
    echo json_encode(array("status" => "sucesso"));
}else{
    echo json_encode(array("status" => "erro"));
}
?>