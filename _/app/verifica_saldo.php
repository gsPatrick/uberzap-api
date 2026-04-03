<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include_once "../classes/transacoes.php";
include_once "../classes/tempo.php";

$c = new Clientes();
$tmp = new tempo();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$valor = $_POST['valor'];

$cliente = $c ->login($telefone, $senha);
$resposta = array();
if($cliente){
    $saldo_atual = $cliente['saldo'];
    $saldo_atual = str_replace(",", ".", $saldo_atual);
    $valor = str_replace(",", ".", $valor);
    if($valor <= $saldo_atual){
        $resposta['status'] = "ok";
        $resposta['mensagem'] = "Saldo suficiente";
    }else{
        $resposta['status'] = "erro";
        $resposta['mensagem'] = "Saldo insuficiente na carteira";
    }
} else{
    $resposta['status'] = "erro";
    $resposta['mensagem'] = "Usuário ou senha inválidos";
}
echo json_encode($resposta);
?>