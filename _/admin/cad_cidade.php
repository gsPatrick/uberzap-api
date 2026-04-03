<?php 
include "seguranca.php";
include_once '../classes/cidades.php';
$c = new cidades();
$nome = $_POST['nome'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$chave = $_POST['chave'];
$chave_mestra = "1234";
if($chave != $chave_mestra){
    echo "<script>alert('Chave de cadastro inválida!'); window.location.href = 'cidades.php';</script>";
    exit;
}
$c -> cadastra($nome, $latitude, $longitude);
echo "<script>alert('Cidade cadastrada com sucesso!'); window.location.href = 'cidades.php';</script>";
?>