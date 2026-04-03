<?php
include("seguranca.php");
include $_SERVER['DOCUMENT_ROOT']."/_/bd/conexao.php";
$id= $_GET['id'];

$apaga= "DELETE FROM motoristas WHERE id = $id";
$deletar = mysqli_query($conexao,$apaga);
if ($deletar) {
	header("Location:listar_motoristas.php");
}else{
	echo "Falha ao deletar";
}

?>