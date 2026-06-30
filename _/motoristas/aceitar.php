<?php
// Resposta é texto puro ('ok'/'no'); warnings impressos corromperiam a resposta.
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");
include("../classes/usuarios_bot_whats.php");
include("../classes/w_api.php");
include("../classes/clientes.php");
include("../classes/expo_push.php");
$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
	$c = new corridas();
	$sh = new status_historico();
	$m = new Motoristas();
	$ubw = new usuarios_bot_whats();
	$id_motorista = $_POST['id_motorista'];
	$id_corrida = $_POST['id_corrida'];
	if (!$id_motorista || !$id_corrida) {
		echo "no";
		exit;
	}
	//verifica se status da corrida é 0
	$corrida = $c->get_corrida_id($id_corrida);
	if (!$corrida) {
		echo "no";
		exit;
	}
	if ($corrida['status'] != 0) {
		echo "no";
		exit;
	}
	$dados_motorista = $m->get_motorista($id_motorista);
	if (!$dados_motorista) {
		echo "no";
		exit;
	}
	$nome_motorista = $dados_motorista['nome'];
	$aceite = $c->aceitar($id_motorista, $id_corrida);
	if (!$aceite) {
		echo "no";
		exit;
	}
	$sh->salva_status($id_corrida, "Motorista " . $nome_motorista . " aceitou a corrida", "App Motorista");

	// Webhook do BOT/IA — passageiro é avisado que o motorista aceitou.
	try {
		require_once __DIR__ . '/../classes/bot_webhook.php';
		BotWebhook::notificarPassageiro($corrida, 'accepted', [
			'nome_motorista' => $dados_motorista['nome'] ?? '',
			'veiculo' => $dados_motorista['veiculo'] ?? '',
			'placa' => $dados_motorista['placa'] ?? '',
		]);
	} catch (Throwable $whErr) {
		error_log('[aceitar.php] BotWebhook: ' . $whErr->getMessage());
	}

	// Notifica o passageiro (via SOL/IA) que o motorista aceitou — best-effort.
	// Sem gate por get_msgs: se a corrida tem user_whatsapp, veio da IA e deve notificar.
	try {
		$user_whatsapp = $corrida['user_whatsapp'] ?? null;
		if ($user_whatsapp) {
			$wapi = new w_api(W_API_TOKEN, W_API_ID);
			$placa = $dados_motorista['placa'] ?? '';
			$veiculo = $dados_motorista['veiculo'] ?? '';

			$mensagem = "🆗 O motorista " . $nome_motorista . " aceitou a sua corrida e está a caminho!\nPlaca: " . $placa . "\nVeículo: " . $veiculo;
			$envio = $wapi->enviarMensagem($user_whatsapp, $mensagem);
			error_log('[aceitar.php] WhatsApp p/ ' . $user_whatsapp . ': ' . json_encode($envio));
		}
	} catch (Throwable $waErr) {
		error_log('[aceitar.php] WhatsApp: ' . $waErr->getMessage());
	}

	// Push para passageiro do app — best-effort.
	try {
		$cl = new Clientes();
		$dados_cliente = $cl->get_cliente_id($corrida['cliente_id'] ?? 0);
		if ($dados_cliente) {
			ExpoPush::notifyPassengerTripStatus($dados_cliente, 1, $nome_motorista, $id_corrida);
		}
	} catch (Throwable $pushErr) {
		error_log('[aceitar.php] Push: ' . $pushErr->getMessage());
	}

	try {
		ExpoPush::notifyOnlineDriversRideUnavailable(
			$corrida['cidade_id'],
			$corrida['categoria_id'] ?? 0,
			$id_corrida,
			$id_motorista,
			'Outro motorista aceitou esta corrida.'
		);
	} catch (Throwable $dErr) {
		error_log('[aceitar.php] notifyDrivers: ' . $dErr->getMessage());
	}

	echo "ok";
}
