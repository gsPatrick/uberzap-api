<?php
include ("seguranca.php");
include("../app/conexao.php");

$loja= $_SESSION['cateloja'];
$id= $_SESSION['id'];
$id_entregador = $_GET['id'];

$ativo= $_GET['ativo'];

$sql = "UPDATE entregadores_$id SET ativo = '$ativo' WHERE id = '$id_entregador'";
		$salvar = mysqli_query($conexao,$sql);
		

if ($salvar){
	header("Location:lista.php");
}else{
    echo "erro, servidor indiponível";
    	header("Location:lista.php");
}

mysqli_close($conexao);
?>