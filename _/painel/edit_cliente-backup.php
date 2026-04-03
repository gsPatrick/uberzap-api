<?php 
include "seguranca.php";
include_once '../classes/clientes.php';
include_once '../classes/transacoes.php';

$c = new clientes();
$t = new transacoes($cidade_id);

$dados_cliente = $c->get_cliente_id($_GET['id']);
$saldo_antigo = str_replace(",", ".", $dados_cliente['saldo']);


$id = $_GET['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$saldo = $_POST['saldo'];
$saldo = str_replace(",", ".", $saldo);
$senha = $_POST['senha'];


if($saldo_antigo < $saldo){
$valor_difereca = $saldo - $saldo_antigo;
$valor_difereca = number_format($valor_difereca, 2, ',', '.');
$t ->insereTransacao($id, "", $valor_difereca, "CREDITO PLATAFORMA", "CONCLUIDO");
}else if($saldo_antigo > $saldo){
$valor_difereca = $saldo_antigo - $saldo;
$valor_difereca = number_format($valor_difereca, 2, ',', '.');
$t ->insereTransacao($id, "", $valor_difereca, "DEBITO PLATAFORMA", "CONCLUIDO");
}

$cadastro = $c->edita($id, $nome, $email, $telefone, $saldo, $senha);
if($cadastro){
    echo "<script>alert('Cliente editado com sucesso!');window.location.href='listar_clientes.php'</script>";
}else{
    echo "<script>alert('Erro ao editar cliente!');window.location.href='listar_clientes.php'</script>";
}

// edit_cliente.php


?>