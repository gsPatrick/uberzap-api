<?php
include("../bd/config.php");
include "../painel/seguranca.php";
include "../classes/corridas.php";
include "../classes/status_historico.php";
include("../classes/usuarios_bot_whats.php");
include("../classes/w_api.php");
include("../classes/motoristas.php");
$ubw = new usuarios_bot_whats();

$c = new corridas();
$sh = new status_historico();
$m = new Motoristas();

$id = $_POST['id'];
$status = $_POST['status'];

$c ->set_status($id, $status);
$sh->salva_status($id, $c->status_string($status), $_POST['origem']); 
//busca dados da corrida
$dados_corrida = $c->get_corrida_id($id);
//verifica se o motorista é do bot
$user_whatsapp = $dados_corrida['user_whatsapp'];
	if ($user_whatsapp) {
		$tem_mensagens = $ubw->get_msgs($user_whatsapp);
		if ($tem_mensagens) {
			$wapi = new w_api(W_API_TOKEN, W_API_ID);
            $id_motorista = $dados_corrida['motorista_id'];
			$dados_motorista = $m->get_motorista($id_motorista);
			if ($status == 2) {
				$wapi->enviarMensagem($user_whatsapp, "📌 O motorista chegou ao local de embarque. Placa do veículo: " . $dados_motorista['placa']);
			} else if ($status == 3) {
				$wapi->enviarMensagem($user_whatsapp, "🚗 A corrida foi iniciada. Boa viagem!");
			} else if ($status == 4) {
				$wapi->enviarMensagem($user_whatsapp, "✅ A corrida foi finalizada. \nValor da corrida: R$ " . $dados_corrida['taxa'] . "\n\nAgradecemos a preferência!");
				//limpa mensagens do usuario
				$ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
			} else if ($status == 5) {
                $wapi->enviarMensagem($user_whatsapp, "❌ A corrida foi cancelada pela central. \n\nAgradecemos a preferência!");
                //limpa mensagens do usuario
                $ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
            }
		}
	}

?>