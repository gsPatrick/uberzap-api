<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../classes/motorista_docs.php");
include("../classes/motoristas.php");
$id= $_GET['id'];

$md = new motorista_docs();
$mt = new motoristas();

$dados_motorista = $md->get_by_id($id);
$inserir = $mt ->cadastrar_motorista(
	$dados_motorista['cidade_id'],
	 $dados_motorista['nome'],
	 $dados_motorista['email'],
	  $dados_motorista['cpf'],
	   $dados_motorista['img_selfie'],
	    $dados_motorista['veiculo'],
		 $dados_motorista['placa'],
		  $dados_motorista['telefone'],
		   $dados_motorista['senha'],
		    0,
			 0,00);

if ($inserir) {
	echo "<script>alert('Motorista aprovado com sucesso!'); window.location.href='lista_motoristas_temp.php';</script>";
} else {
	echo "<script>alert('Erro ao aprovar motorista!'); window.location.href='lista_motoristas_temp.php';</script>";
}

?>