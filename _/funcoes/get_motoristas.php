<?php
header("Access-Control-Allow-Origin: *");
include_once("../classes/motoristas.php");
$e = new motoristas();

$cidade_id = $_POST['cidade_id'];
$motoristas = $e->get_motoristas($cidade_id, true);
echo json_encode($motoristas);
?>