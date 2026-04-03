<?php
include ("../classes/mensagens.php");
include ("../classes/seguranca.php");
$secret_key= $_POST['secret'];

$s = new seguranca();     
if($s->compare_secret($secret_key)){
    $m = new mensagens();
    $id_corrida = $_POST['id_corrida'];
    $msg = $_POST['msg'];
    $sender = $_POST['sender'];
    $m -> insere_msg($id_corrida, $msg, $sender);
    echo "ok";
}


?>