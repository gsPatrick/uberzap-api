<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
$clientes = new Clientes();

$telefone = $_POST['telefone'];
$senha = $_POST['senha'];

$salt = "anjdsn5s141d5";
$senha = md5($senha.$salt);

$cliente = $clientes->verifica_se_existe($telefone);

if($cliente){
    if($cliente['senha'] == $senha){
        $retorno = array(
            "status" => "sucesso",
            "id" => $cliente['id'],
            "nome" => $cliente['nome'],
            "email" => $cliente['email'],
            "telefone" => $cliente['telefone'],
            "ativo" => $cliente['ativo'],
            "saldo" => $cliente['saldo'],
            "cidade_id" => $cliente['cidade_id']
        );
    } else {
        $retorno = array(
            "status" => "erro",
            "erro" => "Telefone ou senha incorretos"
        );
    }
} else {
    $retorno = array(
        "status" => "erro",
        "erro" => "Telefone ou senha incorretos"
    );
}

echo json_encode($retorno);