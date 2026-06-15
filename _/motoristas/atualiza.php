<?php
// A resposta é texto puro ('ok'); qualquer warning/notice impresso corromperia
// a resposta e o app trataria como falha mesmo o status tendo sido salvo.
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/status_historico.php';
require_once __DIR__ . '/../classes/motoristas.php';
require_once __DIR__ . '/../classes/transacoes_motoristas.php';
require_once __DIR__ . '/../classes/transacoes.php';
require_once __DIR__ . '/../classes/clientes.php';
require_once __DIR__ . '/../classes/usuarios_bot_whats.php';
require_once __DIR__ . '/../classes/w_api.php';
require_once __DIR__ . '/../classes/expo_push.php';

$secret_key = $_POST['secret'] ?? '';

$s = new seguranca();
if (!$s->compare_secret($secret_key)) {
    http_response_code(401);
    echo 'no_auth';
    exit;
}

$id_cidade = (int) ($_POST['id_cidade'] ?? 0);
$id_corrida = (int) ($_POST['id_corrida'] ?? 0);
$status = $_POST['status'] ?? '';
$taxa = isset($_POST['taxa']) ? $_POST['taxa'] : null;

if ($id_corrida < 1 || $id_cidade < 1) {
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
    $ubw = new usuarios_bot_whats();

    $c->set_status($id_corrida, $status);
    if ($taxa) {
        $c->atualiza_taxa($id_corrida, $taxa);
    }
    $status_string = $c->status_string($status);
    $sh->salva_status($id_corrida, $status_string, 'App Motorista');

    $dados_corrida = $c->get_corrida_id($id_corrida);
    if (!$dados_corrida) {
        echo 'no';
        exit;
    }

    $id_motorista = (int) ($dados_corrida['motorista_id'] ?? 0);
    $dados_motorista = $id_motorista > 0 ? $m->get_motorista($id_motorista) : null;

    if (($status == '4' || $status == 4) && $dados_motorista) {
        try {
            $saldo_motorista = (float) str_replace(',', '.', (string) ($dados_motorista['saldo'] ?? 0));
            $taxa_pct = (float) str_replace(',', '.', (string) ($dados_motorista['taxa'] ?? 0));
            $valor_corrida = (float) str_replace(',', '.', (string) ($dados_corrida['taxa'] ?? 0));
            $taxa_motorista = $taxa_pct * $valor_corrida / 100;

            $f_pagamento = (string) ($dados_corrida['f_pagamento'] ?? '');

            if ($f_pagamento === 'Carteira Crédito' && !empty($dados_corrida['cliente_id'])) {
                $id_cliente = (int) $dados_corrida['cliente_id'];
                $dados_cliente = $cl->get_cliente_id($id_cliente);
                if ($dados_cliente) {
                    $saldo_cliente = (float) str_replace(',', '.', (string) ($dados_cliente['saldo'] ?? 0));
                    $novo_saldo = number_format($saldo_cliente - $valor_corrida, 2, '.', '');
                    $cl->atualiza_saldo($id_cliente, $novo_saldo);
                    $tc->insereTransacao($id_cliente, 'N/A', number_format($valor_corrida, 2, '.', ''), 'DEBITO CORRIDA', 'CONCLUIDO');
                    $novo_saldo_mot = number_format($saldo_motorista + ($valor_corrida - $taxa_motorista), 2, '.', '');
                    $m->atualiza_saldo($id_motorista, $novo_saldo_mot);
                    $tm->insereTransacao($id_motorista, 'N/A', number_format($valor_corrida, 2, '.', ''), 'CREDITO CORRIDA', 'CONCLUIDO');
                    $tm->insereTransacao($id_motorista, 'N/A', number_format($taxa_motorista, 2, '.', ''), 'DEBITO CORRIDA', 'CONCLUIDO');
                }
            } else {
                $novo_saldo = number_format($saldo_motorista - $taxa_motorista, 2, '.', '');
                $m->atualiza_saldo($id_motorista, $novo_saldo);
                $tm->insereTransacao($id_motorista, 'N/A', number_format($taxa_motorista, 2, '.', ''), 'DEBITO CORRIDA', 'CONCLUIDO');
            }
        } catch (Throwable $finErr) {
            error_log('[atualiza.php] Financeiro: ' . $finErr->getMessage());
        }
    }

    // Notificação WhatsApp (passageiro via SOL/IA) — best-effort, NUNCA quebra o update.
    try {
        $user_whatsapp = $dados_corrida['user_whatsapp'] ?? null;
        error_log('[atualiza.php] corrida=' . $id_corrida . ' status=' . $status . ' user_whatsapp=' . var_export($user_whatsapp, true));
        if ($user_whatsapp && $dados_motorista) {
            $wapi = new w_api(W_API_TOKEN, W_API_ID);
            $envio = null;
            if ($status == 2) {
                $envio = $wapi->enviarMensagem($user_whatsapp, '📌 O motorista chegou ao local de embarque. Placa do veículo: ' . ($dados_motorista['placa'] ?? ''));
            } elseif ($status == 3) {
                $envio = $wapi->enviarMensagem($user_whatsapp, '🚗 A corrida foi iniciada. Boa viagem!');
            } elseif ($status == 4) {
                $envio = $wapi->enviarMensagem($user_whatsapp, "✅ A corrida foi finalizada.\nValor da corrida: R$ " . ($dados_corrida['taxa'] ?? '') . "\n\nAgradecemos a preferência!");
                $ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
            }
            error_log('[atualiza.php] WhatsApp status=' . $status . ' envio=' . json_encode($envio));
        }
    } catch (Throwable $waErr) {
        error_log('[atualiza.php] WhatsApp: ' . $waErr->getMessage());
    }

    // Push (passageiro do app) — best-effort, NUNCA quebra o update.
    try {
        $id_cliente = $dados_corrida['cliente_id'] ?? null;
        if ($id_cliente && $dados_motorista) {
            $dados_cliente_push = $cl->get_cliente_id($id_cliente);
            if ($dados_cliente_push) {
                $st = (int) $status;
                if (in_array($st, [2, 3, 4, 5], true)) {
                    ExpoPush::notifyPassengerTripStatus(
                        $dados_cliente_push,
                        $st,
                        $dados_motorista['nome'] ?? 'Motorista',
                        $id_corrida
                    );
                }
            }
        }
    } catch (Throwable $pushErr) {
        error_log('[atualiza.php] Push: ' . $pushErr->getMessage());
    }

    if (($status == '4' || $status == 4) && $id_motorista > 0) {
        $m->atualiza_disponibilidade($id_motorista, 1);
    }

    echo 'ok';
} catch (Throwable $e) {
    error_log('[atualiza.php] ' . $e->getMessage());
    http_response_code(500);
    echo 'erro';
}
