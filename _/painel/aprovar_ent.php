<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("../app/conexao.php");
$id= $_GET['id'];



$sql = mysqli_query($conexao, "SELECT * FROM motorista_docs WHERE id = '$id'");
$resultado = mysqli_fetch_assoc($sql);
$id_pai = $resultado['id_pai'];


$ins_sql =  "INSERT INTO entregadores_$id_pai
(nome, rg, cpf, img, veiculo, placa, ativo, latitude, longitude, telefone, senha, raio, nota, id_signal, taxa) 
        VALUES ('$resultado[nome]','0','$resultado[cpf]','$resultado[img_selfie]',
		'$resultado[veiculo]','$resultado[placa]','1','0','0','$resultado[telefone]',
        '$resultado[senha]', '15', '0','0','10')";

$inserir = mysqli_query($conexao,$ins_sql);



if ($inserir) {
	header("Location:lista_motoristas_temp.php?ins=ok");
}else{
	$erro = mysqli_error($conexao);
	echo "Falha ao aprovar erro:".$erro;
}

?>