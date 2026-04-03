<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../app/conexao.php");
$id= $_GET['id'];
$location= $_GET['location'];
$categoria= $_GET['categoria'];


$apaga= "DELETE FROM $categoria WHERE id = $id";
$deletar = mysqli_query($conexao,$apaga);
if ($deletar) {
	header("Location:$location");
}else{
	echo "Falha ao deletar";
}

?>