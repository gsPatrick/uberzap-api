<?php 
include_once "../classes/dinamico_mapa.php";
$dm = new dinamico_mapa();
$cidade_id = $_GET['cidade_id'];

$dados = $dm->get_dinamico_mapa($cidade_id);
echo json_encode($dados);
?>