<?php
include ("../classes/seguranca.php");
include ("../classes/motoristas.php");
$secret_key= $_POST['secret'];

$s = new seguranca();     
if($s->compare_secret($secret_key)){
	$id_motorista = $_POST['id_motorista'];
	$m = new Motoristas();
	$status = $_POST['status'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$m -> atualiza_coordenadas($id_motorista, $latitude, $longitude);
	$m -> atualiza_disponibilidade($id_motorista, $status);
}

?>