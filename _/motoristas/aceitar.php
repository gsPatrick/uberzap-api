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
	// DIAGNÓSTICO: registra TODA chamada de aceite (pra ver se cai na nossa API).
	require_once __DIR__ . '/../classes/uzlog.php';
	uzlog("[aceitar] corrida=$id_corrida motorista=$id_motorista status_atual=" . ($corrida['status'] ?? '?') . " wpp=" . ((($corrida['user_whatsapp'] ?? '') !== '') ? '1' : '0'));
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

	// (O aviso "motorista aceitou" ao passageiro sai SÓ pela IA Sol, via o
	// webhook acima. Envio direto por WhatsApp REMOVIDO pra não duplicar.)

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
