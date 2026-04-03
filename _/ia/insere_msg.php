<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/usuarios_bot_whats.php");

$ubw = new usuarios_bot_whats();

$user_id = $_POST['user_id'];
$msg_recebida = $_POST['msg_recebida'];
$msg_enviada = $_POST['msg_enviada'];

$ubw->insere($user_id, $msg_recebida, $msg_enviada);

echo json_encode(array('status' => 'ok'));
?>