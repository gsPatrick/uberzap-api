<?php 
header('access-control-allow-origin: *');
include_once "../classes/transacoes_motoristas.php";

$cidade_id = $_POST['cidade_id'];
$t = new transacoes_motoristas($cidade_id);

$user_id = $_POST['user_id'];
$ref = $_POST['ref'];
$valor = $_POST['valor'];
$link = $_POST['link'];
$metodo = "RECARGA ONLINE";
$status = "PENDENTE";

$t->insereTransacao($user_id, $ref, $valor, $metodo, $status, $link);
echo json_encode(array('status' => 'ok'));
?>