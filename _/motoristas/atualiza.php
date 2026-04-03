<?php
header('Access-Control-Allow-Origin: *');
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");
include("../classes/transacoes_motoristas.php");
include("../classes/transacoes.php");
include("../classes/clientes.php");
include("../classes/usuarios_bot_whats.php");
include("../classes/w_api.php");
$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
	$id_cidade = $_POST['id_cidade'];
	$c = new corridas();
	$sh = new status_historico();
	$m = new Motoristas();
	$tm = new transacoes_motoristas($id_cidade);
	$tc = new transacoes($id_cidade);
	$cl = new Clientes();
	$ubw = new usuarios_bot_whats();
	$id_corrida = $_POST['id_corrida'];
	$status = $_POST['status'];
	$c->set_status($id_corrida, $status);
	$status_string = $c->status_string($status);
	$sh->salva_status($id_corrida, $status_string, "App Motorista");
	//busca dados da corrida wa
	$dados_corrida = $c->get_corrida_id($id_corrida);
	$id_motorista = $dados_corrida['motorista_id'];
	$dados_motorista = $m->get_motorista($id_motorista);
	//DESCONTA TAXA DO MOTORISTA
	if ($status == '4' || $status == 4) {
		

		$saldo_motorista = $dados_motorista['saldo'];
		$saldo_motorista = str_replace(',', '.', $saldo_motorista);
		$taxa_motorista = $dados_motorista['taxa'];
		$taxa_motorista = str_replace(',', '.', $taxa_motorista);
		$valor_corrida = $dados_corrida['taxa'];
		$valor_corrida = str_replace(',', '.', $valor_corrida);
		$taxa_motorista = $taxa_motorista * $valor_corrida / 100;



		//desconta taxa do cliente se f_pagamento = "Carteira Crédito"
		$f_pagamento = $dados_corrida['f_pagamento'];
		if ($f_pagamento == "Carteira Crédito") {
			$id_cliente = $dados_corrida['cliente_id'];
			$dados_cliente = $cl->get_cliente_id($id_cliente);
			$saldo_cliente = $dados_cliente['saldo'];
			$saldo_cliente = str_replace(',', '.', $saldo_cliente);
			$novo_saldo = $saldo_cliente - $valor_corrida;
			$novo_saldo = number_format($novo_saldo, 2, ',', '');
			$cl->atualiza_saldo($id_cliente, $novo_saldo);
			$tc->insereTransacao($id_cliente, 'N/A', $valor_corrida, 'DEBITO CORRIDA', 'CONCLUIDO');
			//passa o valor da corrida para o motorista - a taxa
			$novo_saldo = $saldo_motorista + ($valor_corrida - $taxa_motorista);
			$novo_saldo = number_format($novo_saldo, 2, ',', '');
			$m->atualiza_saldo($id_motorista, $novo_saldo);
			$valor_corrida = number_format($valor_corrida, 2, ',', '');
			$tm->insereTransacao($id_motorista, 'N/A', $valor_corrida, 'CREDITO CORRIDA', 'CONCLUIDO');
			$taxa_motorista = number_format($taxa_motorista, 2, ',', '');
			$tm->insereTransacao($id_motorista, 'N/A', $taxa_motorista, 'DEBITO CORRIDA', 'CONCLUIDO');
		} else { //caso não seja carteira crédito, desconta a taxa do motorista
			$novo_saldo = $saldo_motorista - $taxa_motorista;
			$novo_saldo = number_format($novo_saldo, 2, ',', '');
			$m->atualiza_saldo($id_motorista, $novo_saldo);
			$taxa_motorista = number_format($taxa_motorista, 2, ',', '');
			$tm->insereTransacao($id_motorista, 'N/A', $taxa_motorista, 'DEBITO CORRIDA', 'CONCLUIDO');
		}
	}
	$user_whatsapp = $dados_corrida['user_whatsapp'];
	if ($user_whatsapp) {
		$tem_mensagens = $ubw->get_msgs($user_whatsapp);
		if ($tem_mensagens) {
			$wapi = new w_api(W_API_TOKEN, W_API_ID);
			$dados_motorista = $m->get_motorista($id_motorista);
			if ($status == 2) {
				$wapi->enviarMensagem($user_whatsapp, "📌 O motorista chegou ao local de embarque. Placa do veículo: " . $dados_motorista['placa']);
			} else if ($status == 3) {
				$wapi->enviarMensagem($user_whatsapp, "🚗 A corrida foi iniciada. Boa viagem!");
			} else if ($status == 4) {
				$wapi->enviarMensagem($user_whatsapp, "✅ A corrida foi finalizada. \nValor da corrida: R$ " . $dados_corrida['taxa'] . "\n\nAgradecemos a preferência!");
				//limpa mensagens do usuario
				$ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
			}
		}
	}
	echo "ok";
}
