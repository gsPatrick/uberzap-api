<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../app/conexao.php");
$id= $_GET['id'];


$apaga= "DELETE FROM users WHERE id = $id";
$deletar = mysqli_query($conexao,$apaga);
if ($deletar) {
	header("Location:lista.php");
}else{
	echo "Falha ao deletar";
}

?>