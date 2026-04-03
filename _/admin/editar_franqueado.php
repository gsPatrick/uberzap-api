<?php 
include("seguranca.php");
include_once("../classes/franqueados.php");
include_once("../classes/cobranca.php");
$u = new franqueados(); 
$c = new cobranca();
$nome = $_POST['nome'];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$cidade_id = $_POST['cidade_id'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$comissao = $_POST['comissao'];

//edita usuario
if(isset($_POST['id'])){
	$id = $_POST['id'];
	$u->edit_usuario($id, $nome, $usuario, $senha, $cidade_id, $comissao, $telefone, $email);
	echo '<script>alert("Franqueado editado com sucesso!");</script>';
	echo '<script>window.location.href="franqueados.php";</script>';
}
