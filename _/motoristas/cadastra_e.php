<?php
ini_set('default_charset','UTF-8');
include ("conexao.php");
$id_pai= $_POST['id'];
$secret_key= $_POST['secret'];
$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$veiculo = $_POST['veiculo'];
$placa = $_POST['placa'];
$senha = $_POST['senha'];
$id_signal = $_POST['id_signal'];
$raio = $_POST['raio'];





if($secret_key==$secret){
		$edita= "UPDATE entregadores_$id_pai SET nome = '$nome', email = '$email', veiculo = '$veiculo', placa = '$placa', telefone = '$telefone', senha = '$senha', raio = '$raio' WHERE cpf = '$cpf'";
		$up = mysqli_query($conexao,$edita);
		if ($up) {
			echo "ok";

		}else{
			echo "error";
		}
}else{
	echo "Falha de autenticação";
}

?>