<?php
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/status_historico.php';
require_once __DIR__ . '/../classes/motoristas.php';
require_once __DIR__ . '/../classes/transacoes_motoristas.php';
require_once __DIR__ . '/../classes/transacoes.php';
require_once __DIR__ . '/../classes/clientes.php';
require_once __DIR__ . '/../classes/expo_push.php';

$secret_key = $_POST['secret'] ?? '';

$s = new seguranca();
if (!$s->compare_secret($secret_key)) {
    http_response_code(401);
    echo 'no_auth';
    exit;
}

$id_cidade = (int) ($_POST['id_cidade'] ?? 0);
$id_motorista = (int) ($_POST['id_motorista'] ?? 0);
$id_corrida = (int) ($_POST['id_corrida'] ?? 0);
$taxa = str_replace(',', '.', (string) ($_POST['taxa'] ?? '0'));
$tempo = (string) ($_POST['tempo'] ?? '1');
$km = str_replace(',', '.', (string) ($_POST['km'] ?? '0'));
$endereco_fim = trim((string) ($_POST['endereco_fim'] ?? 'Destino não informado'));
$lat_fim = trim((string) ($_POST['lat_fim'] ?? ''));
$lng_fim = trim((string) ($_POST['lng_fim'] ?? ''));

if ($id_motorista < 1 || $id_corrida < 1 || $id_cidade < 1) {
    http_response_code(400);
    echo 'no';
    exit;
}

try {
    $c = new corridas();
    $sh = new status_historico();
    $m = new Motoristas();
    $tm = new transacoes_motoristas($id_cidade);
    $tc = new transacoes($id_cidade);
    $cl = new Clientes();

    $corrida = $c->get_corrida_id($id_corrida);
    if (!$corrida) {
        echo 'no';
        exit;
    }

    if ((int) ($corrida['motorista_id'] ?? 0) !== $id_motorista) {
        http_response_code(403);
        echo 'no';
        exit;
    }

    $dados_motorista = $m->get_motorista($id_motorista);
    if (!$dados_motorista) {
        echo 'no';
        exit;
    }

    $statusAtual = (int) ($corrida['status'] ?? 0);

    // Idempotente: corrida já finalizada — só garante motorista online
    if ($statusAtual === 4) {
        $m->atualiza_disponibilidade($id_motorista, 1);
        echo 'ok';
        exit;
    }

    $valor_corrida = max(0, (float) $taxa);
    $taxa_fmt = number_format($valor_corrida, 2, '.', '');

    // 1) Finaliza corrida primeiro (passageiro/motorista veem status 4)
    $c->update_taximetro($id_corrida, $taxa_fmt, $tempo, $km, $endereco_fim, $lat_fim, $lng_fim);
    $c->atualiza_taxa($id_corrida, $taxa_fmt);
    $c->set_status($id_corrida, 4);
    $sh->salva_status($id_corrida, 'Taxímetro finalizado', 'App Motorista');

    // 2) Financeiro — não impede finalização se der erro pontual
    try {
        $saldo_motorista = (float) str_replace(',', '.', (string) ($dados_motorista['saldo'] ?? 0));
        $taxa_pct = (float) str_replace(',', '.', (string) ($dados_motorista['taxa'] ?? 0));
        $taxa_motorista = $taxa_pct * $valor_corrida / 100;

        $f_pagamento = (string) ($corrida['f_pagamento'] ?? '');

        if ($f_pagamento === 'Carteira Crédito' && !empty($corrida['cliente_id'])) {
            $id_cliente = (int) $corrida['cliente_id'];
            $dados_cliente = $cl->get_cliente_id($id_cliente);
            if ($dados_cliente) {
                $saldo_cliente = (float) str_replace(',', '.', (string) ($dados_cliente['saldo'] ?? 0));
                $novo_saldo_cliente = number_format($saldo_cliente - $valor_corrida, 2, '.', '');
                $cl->atualiza_saldo($id_cliente, $novo_saldo_cliente);
                $tc->insereTransacao(
                    $id_cliente,
                    'N/A',
                    number_format($valor_corrida, 2, '.', ''),
                    'DEBITO CORRIDA',
                    'CONCLUIDO'
                );
                $novo_saldo_mot = number_format($saldo_motorista + ($valor_corrida - $taxa_motorista), 2, '.', '');
                $m->atualiza_saldo($id_motorista, $novo_saldo_mot);
                $tm->insereTransacao(
                    $id_motorista,
                    'N/A',
                    number_format($valor_corrida, 2, '.', ''),
                    'CREDITO CORRIDA',
                    'CONCLUIDO'
                );
                $tm->insereTransacao(
                    $id_motorista,
                    'N/A',
                    number_format($taxa_motorista, 2, '.', ''),
                    'DEBITO CORRIDA',
                    'CONCLUIDO'
                );
            }
        } else {
            $novo_saldo = number_format($saldo_motorista - $taxa_motorista, 2, '.', '');
            $m->atualiza_saldo($id_motorista, $novo_saldo);
            $tm->insereTransacao(
                $id_motorista,
                'N/A',
                number_format($taxa_motorista, 2, '.', ''),
                'DEBITO TAXIMETRO',
                'CONCLUIDO'
            );
        }
    } catch (Throwable $finErr) {
        error_log('[atualiza_taxi.php] Financeiro: ' . $finErr->getMessage());
    }

    // 3) Push passageiro
    if (!empty($corrida['cliente_id'])) {
        $dados_cliente = $cl->get_cliente_id($corrida['cliente_id']);
        if ($dados_cliente) {
            ExpoPush::notifyPassengerTripStatus(
                $dados_cliente,
                4,
                $dados_motorista['nome'] ?? 'Motorista',
                $id_corrida
            );
        }
    }

    // 4) Motorista disponível para novas corridas (online = 1)
    $m->atualiza_disponibilidade($id_motorista, 1);

    echo 'ok';
} catch (Throwable $e) {
    error_log('[atualiza_taxi.php] ' . $e->getMessage());
    http_response_code(500);
    echo 'erro';
}
