<?php
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");
include("../classes/usuarios_bot_whats.php");
include("../classes/w_api.php");
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
	$user_whatsapp = $corrida['user_whatsapp'];
	if ($user_whatsapp) {
		$tem_mensagens = $ubw->get_msgs($user_whatsapp);
		if ($tem_mensagens) {
			$wapi = new w_api(W_API_TOKEN, W_API_ID);
			$dados_motorista = $m->get_motorista($id_motorista);
			$nome_motorista = $dados_motorista['nome'];
			$placa = $dados_motorista['placa'];
			$veiculo = $dados_motorista['veiculo'];

			$mensagem = "🆗 Corrida aceita por " . $nome_motorista . "\nPlaca: " . $placa . "\nVeículo: " . $veiculo;
			$envio = $wapi->enviarMensagem($user_whatsapp, $mensagem);
		}
	}
	echo "ok";
}
