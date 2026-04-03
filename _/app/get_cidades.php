<?php 
header('access-control-allow-origin: *');
include_once "../classes/cidades.php";

$c = new Cidades();

$cidades = $c->get_array_cidades();
echo json_encode($cidades);
?>