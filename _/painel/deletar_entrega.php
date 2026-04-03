<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../app/conexao.php");
$id= $_GET['id'];
$cpf= $_GET['cpf'];
$nome= $_GET['nome'];
session_start();
$id= $_SESSION['id'];

$apaga= "DELETE FROM entregadores_historico_$id WHERE id = $id_entregador";
$deletar = mysqli_query($conexao,$apaga);
if ($deletar) {
	header("Location:detalhar_entregador.php?nome=$nome&cpf=$cpf");
}else{
	echo "Falha ao deletar";
}

?>