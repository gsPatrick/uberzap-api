<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include_once "../classes/clientes.php";

$c = new clientes();
$id= $_GET['id'];
$ativo = $_GET['ativo'];

$c ->ativar_desativar($id, $ativo);
echo '<script>window.location.href="listar_clientes.php";</script>';


?>