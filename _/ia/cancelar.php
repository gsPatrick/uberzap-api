<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/alertas_motoristas.php";
include("../classes/usuarios_bot_whats.php");
include("../classes/w_api.php"); 
include("../classes/status_historico.php");

$crr = new corridas();
$am = new alertas_motoristas();
$ubw = new usuarios_bot_whats();
$sh = new status_historico();


$user_id = $_POST['user_id'];

$msgs = $ubw->get_msgs($user_id);
if($msgs){
    $ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_id);
    $dados_corrida = $crr -> getCorridaByWA($user_id);
    $id_motorista = $dados_corrida['motorista_id'];
    $crr -> cancelarCorridaWA($user_id);
    $wapi = new w_api(W_API_TOKEN, W_API_ID);
    $mensagem = "🚫 Corrida cancelada.";
    $envio = $wapi->enviarMensagem($user_id, $mensagem);

    //insere alerta de cancelamento
    $am->insere($id_motorista, "Corrida cancelada pelo cliente");

    echo json_encode(array('status' => 'ok'));
}else{
    echo json_encode(array('status' => 'erro'));
}



?>