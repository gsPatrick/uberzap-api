<?php
include("seguranca.php");
include("../app/conexao.php");

//entrada de formulário
$email = $_POST['email'];
$nome = $_POST['nome'];
$senha = $_POST['senha'];
$status= $_POST['status'];
$cpf= $_POST['cpf'];
$cidade= $_POST['cidade'];
$estado= $_POST['estado'];
$app_nome= $_POST['app_nome'];
$telefone= $_POST['fone'];
$taxa = $_POST['taxa'];
$id = $_GET['id'];






  


	        	$sql = "UPDATE users SET email = '$email', 
				senha = '$senha',
				nome = '$nome',
				cpf = '$cpf',
				cidade = '$cidade',
				estado = '$estado', 
				status = '$status',
				taxa = '$taxa',
				nome_app= '$app_nome', 
				telefone= '$telefone'
				WHERE id = '$id'";

				$salvar = mysqli_query($conexao,$sql);
				header("Location:lista.php");
				//echo $email." - ".$senha." - ".$saldo." - ".$desconto." - ".$cpf." - ".$nome." - ".$telefone." - ".$cidade." - ". //debug
				//$estado." - ".$app_nome." - ".$status." - ".$desconto; //debug
    
mysqli_close($conexao);
?>