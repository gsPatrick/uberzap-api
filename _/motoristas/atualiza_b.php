<?php
// Versão defensiva: evita notices/warnings que quebram o JSON de resposta.
// Em produção, deixe display_errors desativado no php.ini.

// header JSON e limpeza de buffer
header('Content-Type: application/json; charset=utf-8');
if (ob_get_length()) ob_clean();

//habilita exibição de erros
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");
include("../classes/transacoes_motoristas.php");
include("../classes/transacoes.php"); 
include("../classes/clientes.php");
include("../classes/localizacao_corridas.php"); 
include("../classes/maps.php");
include("../classes/tempo.php");
include("../classes/categorias.php");
include("../classes/dinamico_mapa.php");
include("../classes/categorias_horarios.php");
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
	$lc = new localizacao_corridas();
	$mps = new Maps();
	$tmp = new Tempo();
	$cat = new Categorias();
	$dm = new dinamico_mapa();
	$dh = new dinamico_horarios();
	$ubw = new usuarios_bot_whats();

	$id_corrida = $_POST['id_corrida'];
	$status = $_POST['status'];
	$c->set_status($id_corrida, $status);
	$status_string = $c->status_string($status);
	$sh->salva_status($id_corrida, $status_string, "App Motorista");
	$valor_corrida = 0;
	$dados_corrida = $c->get_corrida_id($id_corrida);
	//DESCONTA TAXA DO MOTORISTA
	if ($status == '4' || $status == 4) {
		
		$coordenadas_corrida = $lc->getByCorridaId($id_corrida);

		$lat_ini = $dados_corrida['lat_ini'];
		$lng_ini = $dados_corrida['lng_ini'];
		$lat_fim = $dados_corrida['lat_fim'];
		$lng_fim = $dados_corrida['lng_fim'];

		$waypoints = array();
		foreach ($coordenadas_corrida as $key => $value) {
			$waypoints[] = $value['latitude'] . ',' . $value['longitude'];
		}
// DESATIVA O RECÁLCULO e usa os valores salvos na estimativa
		//$km = $mps->calcularRota($lat_ini, $lng_ini, $waypoints, $lat_fim, $lng_fim);

		$tmp->data_fim = $dados_corrida['date'];
		$tmp->data_ini = date('Y-m-d H:i:s');
		//$minutos = $tmp->tempo_passou("minutos");

		$categoria_id = $dados_corrida['categoria_id'];
		$dados_categoria = $cat->get_categoria($categoria_id)[0];

		$taxa_km = $dados_categoria['tx_km'];
		$taxa_km = str_replace(",", ".", $taxa_km);
		$taxa_minuto = $dados_categoria['tx_minuto'];
		$taxa_minuto = str_replace(",", ".", $taxa_minuto);
		$taxa_base = $dados_categoria['tx_base'];
		$taxa_base = str_replace(",", ".", $taxa_base);

		$taxa_corrida = ($km * $taxa_km) + ($minutos * $taxa_minuto) + $taxa_base;

		//verifica se está dentro do dinamico de horarios
		$taxa_add = 0;
		$taxa_add_horarios = 0;
		$dinamico_horarios = $dados_categoria['dinamico_horarios'];
		foreach ($dinamico_horarios as $horario) {
			$taxa_h = $dh->verifica_horario($horario);
			if ($taxa_h) {
				$taxa_add = str_replace(",", ".", $taxa_h['adicional']);
				if ($taxa_add > $taxa_add_horarios) {
					$taxa_add_horarios = $taxa_add;
					$dados_retorno['dinamico_horarios'] = $taxa_h;
				}
			}
		}
		//fim verifica se está dentro do dinamico de horarios

		//verifica se está dentro do dinamico de mapa e pega o mais caro
		$taxa_add_mapa_end_ini = 0;
		$taxa_add = 0;
		$dinamico_mapa = $dados_categoria['dinamico_local'];
		foreach ($dinamico_mapa as $din_mapa) {
			$taxa_m = $dm->verifica_localizacao($cidade_id, $lat_ini, $lng_ini);
			if ($taxa_m) {
				$tx_add = str_replace(",", ".", $taxa_m['adicional']);
				if ($tx_add > $taxa_add_mapa_end_ini) {
					$taxa_add_mapa_end_ini = $tx_add;
					$dados_retorno['dinamico_mapa_ini'] = $taxa_m;
				}
			}
		}
		//fim verifica se está dentro do dinamico de mapa

		$taxa_add = 0;
		$taxa_add_mapa_end_fim = 0;
		$dinamico_mapa = $dados_categoria['dinamico_local'];
		foreach ($dinamico_mapa as $din_mapa) {
			$taxa_m = $dm->verifica_localizacao($cidade_id, $lat_fim, $lng_fim);
			if ($taxa_m) {
				$taxa_add = str_replace(",", ".", $taxa_m['adicional']);
				if ($taxa_add > $taxa_add_mapa_end_fim) {
					$taxa_add_mapa_end_fim = $taxa_add;
					$dados_retorno['dinamico_mapa_fim'] = $taxa_m;
				}
			}
		}

		$taxa_corrida += $taxa_add_horarios + $taxa_add_mapa_end_ini + $taxa_add_mapa_end_fim;



		$id_motorista = $dados_corrida['motorista_id'];
		$dados_motorista = $m->get_motorista($id_motorista);
		$saldo_motorista = $dados_motorista['saldo'];
		$saldo_motorista = str_replace(',', '.', $saldo_motorista);
		$taxa_motorista = $dados_motorista['taxa'];
		$taxa_motorista = str_replace(',', '.', $taxa_motorista);
		$valor_corrida = $dados_corrida['taxa'];

		//verifica se o valor atual da corrida é menor que o valor da corrida calculado se for, atualiza o valor da corrida (alterado de true para false para desativar o recalculo)
		if (false) {
			$c->atualiza_taxa($id_corrida, number_format($taxa_corrida, 2, ',', ''));
			$valor_corrida = $taxa_corrida;
			//atualiza os km
			$c->atualizaKm($id_corrida, number_format($km, 2, ',', ''));
		}

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
	// === MENSAGENS WHATSAPP ===
$user_whatsapp = isset($dados_corrida['user_whatsapp']) ? $dados_corrida['user_whatsapp'] : null;
if ($user_whatsapp) {
    $tem_mensagens = $ubw->get_msgs($user_whatsapp);
    if ($tem_mensagens) {
        $wapi = new w_api(W_API_TOKEN, W_API_ID);
        // pega motorista atualizado
        $dados_motorista = $id_motorista ? $m->get_motorista($id_motorista) : null;
        try {
            if ($status == 2) {
                $wapi->enviarMensagem($user_whatsapp, "📌 O motorista chegou ao local de embarque. Placa: " . ($dados_motorista['placa'] ?? ''));
            } elseif ($status == 3) {
                $wapi->enviarMensagem($user_whatsapp, "🚗 A corrida foi iniciada. Boa viagem!");
            } elseif ($status == 4) {
                $wapi->enviarMensagem($user_whatsapp, "✅ Corrida finalizada.\nValor: R$ " . number_format($valor_corrida, 2, ',', '.') . "\n\nAgradecemos a preferência!");
                $ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
            }
        } catch (Throwable $t) {
            // não interrompe processo se falhar a mensagem
        }
    }
}
	$valor_corrida = number_format($valor_corrida, 2, ',', '');
	$taxa_corrida = number_format($taxa_corrida, 2, ',', '');
	echo json_encode(array(
		"status" => "ok",
		"msg" => "Status atualizado com sucesso",
		"valor_corrida" => $valor_corrida,
		"forma_pagamento" => $f_pagamento,
		"taxa_corrida" => $taxa_corrida
	));
}