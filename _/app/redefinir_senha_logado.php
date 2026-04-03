<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
$clientes = new Clientes();

$id = $_POST['cliente_id'];
$senha = $_POST['nova_senha'];
$senha_atual = $_POST['senha_atual'];

$salt = "anjdsn5s141d5";
$senha_atual = md5($senha_atual.$salt);

$cliente = $clientes->get_cliente_id($id);

if($cliente){
    if($cliente['senha'] == $senha_atual){
        $senha = md5($senha.$salt);
        $clientes->redefinir_senha($id, $senha);
        $retorno = array(
            "status" => "sucesso",
            "mensagem" => "Senha alterada com sucesso"
        );
    } else {
        $retorno = array(
            "status" => "erro",
            "mensagem" => "Senha atual incorreta"
        );
    }
} else {
    $retorno = array(
        "status" => "erro",
        "mensagem" => "Cliente não encontrado"
    );
}

echo json_encode($retorno);