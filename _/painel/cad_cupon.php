<?php 
include "seguranca.php";
include_once "../classes/cupons.php";
$c = new cupons();
$nome = $_POST['nome'];
$valor_min = $_POST['valor_min'];
$primeira_compra = $_POST['primeira_compra'];
$validade = $_POST['validade'];
$quantidade = $_POST['quantidade'];
$hora = $_POST['hora'];
$validade = $validade." ".$hora;
$validade = date('Y-m-d H:i:s', strtotime($validade));
$tipo_desconto = $_POST['tipo_desconto'];
$uso_unico = $_POST['uso_unico'];
if($tipo_desconto == 2){
    $valor = $_POST['valor_reais'];
}else{
    $valor = $_POST['valor_porcentagem'];
}

$c->cadastra_cupon($cidade_id, $nome, $valor, $valor_min, $primeira_compra, $validade, $quantidade, $uso_unico, $tipo_desconto);
echo "<script>alert('Cupom cadastrado com sucesso!');window.location.href='../painel/listar_cupons.php';</script>";
?>