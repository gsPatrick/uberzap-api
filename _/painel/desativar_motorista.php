<?php
ini_set('default_charset','UTF-8');
include("seguranca.php");
include_once $_SERVER['DOCUMENT_ROOT']."/_/bd/conexao.php";
$id= $_GET['id'];
$status = $_GET['status'];

$up= "UPDATE motoristas SET ativo = '$status' WHERE id = $id";
$muda = mysqli_query($conexao,$up);
if ($status == '2') {
    echo '<script>alert("Motorista desativado com sucesso!");</script>';
    echo '<script>window.location.href="listar_motoristas.php";</script>';
}else{
    echo '<script>alert("Motorista ativado com sucesso!");</script>';
    echo '<script>window.location.href="listar_motoristas.php";</script>';
}

?>