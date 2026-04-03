<?php 
include("seguranca.php");
include_once("../classes/franqueados.php");
include_once("../classes/cobranca.php");
$u = new franqueados(); 
$c = new cobranca();
$nome = $_POST['nome'];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$cidade_id = $_GET['cidade_id'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$comissao = $_POST['comissao'];



$id = $u->cadastra_usuario($nome, $usuario, $senha, $cidade_id, $comissao, $telefone, $email);


echo '<script>alert("Franqueado cadastrado com sucesso!");</script>';
echo '<script>window.location.href="franqueados.php";</script>';
?>