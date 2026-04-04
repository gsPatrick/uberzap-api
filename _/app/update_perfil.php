<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
$clientes = new Clientes();

$id = $_POST['cliente_id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];

// Busca os dados atuais do cliente para manter o saldo (ou outros campos se necessário)
$dados_atuais = $clientes->get_cliente_id($id);

if(!$dados_atuais){
    echo json_encode(array("status" => "Erro: Usuário não encontrado"));
    exit;
}

$saldo = $dados_atuais['saldo'];

// Edita o cliente
$atualiza = $clientes->edita($id, $nome, $email, $telefone, $saldo);

if($atualiza){
    echo json_encode(array("status" => "sucesso"));
}else{
    echo json_encode(array("status" => "erro", "mensagem" => "Falha ao atualizar banco de dados"));
}
?>
