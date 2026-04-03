<?php
include("seguranca.php");
include "../classes/motoristas.php";
include "../classes/upload.php";
include "../classes/transacoes_motoristas.php";
$m = new motoristas();
$t = new transacoes_motoristas($cidade_id);

//entrada de formulário
$id_motorista=$_GET['id'];
$dados = $m->get_motorista($id_motorista);
$saldo_antigo = str_replace(",", ".", $dados['saldo']);

 

$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf= $_POST['cpf'];
$veiculo= $_POST['veiculo'];
$placa = $_POST['placa'];
$telefone= $_POST['telefone'];
$senha= $_POST['senha'];
$taxa = $_POST['taxa'];
$ids_categorias = $_POST['ids_categorias'];

$saldo = $_POST['saldo'];
$saldo = str_replace(",", ".", $saldo);

if($saldo_antigo < $saldo){
$valor_difereca = $saldo - $saldo_antigo;
$valor_difereca = number_format($valor_difereca, 2, ',', '.');
$t ->insereTransacao($id_motorista, "", $valor_difereca, "CREDITO PLATAFORMA", "CONCLUIDO");
}else if($saldo_antigo > $saldo){
$valor_difereca = $saldo_antigo - $saldo;
$valor_difereca = number_format($valor_difereca, 2, ',', '.');
$t ->insereTransacao($id_motorista, "", $valor_difereca, "DEBITO PLATAFORMA", "CONCLUIDO");
}

//verifica se possui nova imagem
//parametros para imagem
$pasta='../admin/uploads/';
$img=$_FILES['img'];
if($img['name'] != ""){
    $upload = new Upload($img, 800, 800, $pasta);
    $nome_img = $upload->salvar();
}else{
	$nome_img = $dados['img'];
}

//atualiza dados
$m->edit_motorista($id_motorista, $nome, $email, $cpf, $nome_img, $veiculo, $placa, $telefone, $senha, $taxa, $saldo, $ids_categorias);

echo "<script>alert('Dados atualizados com sucesso!');</script>";
echo "<script>window.location.href='listar_motoristas.php';</script>";


?>