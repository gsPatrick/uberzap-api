<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../app/conexao.php");
$id= $_GET['id'];


$apaga= "DELETE FROM motorista_docs WHERE id = '$id'";
$deletar = mysqli_query($conexao,$apaga);
if ($deletar) {
	header("Location:lista_motoristas_temp.php");
}else{
	echo "Falha ao deletar";
}

?>