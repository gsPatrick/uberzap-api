<?php
// A resposta é texto puro ('ok'); qualquer warning/notice impresso corromperia
// a resposta e o app trataria como falha mesmo o status tendo sido salvo.
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../bd/config.php'; // define W_API_ID / W_API_TOKEN / PATCH_LIMPA_MSG (faltava -> WhatsApp de status nao saia)
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

    // Lê o status ANTES pra detectar mudança REAL. O app pode reenviar o MESMO
    // status várias vezes (o taxímetro reenvia) — sem essa guarda, o webhook
    // duplicaria a mensagem da IA e, pior, o financeiro (status 4) debitaria o
    // motorista em dobro. Só dispara os efeitos se o status realmente mudou.
    $dados_antes = $c->get_corrida_id($id_corrida);
    $status_anterior = $dados_antes ? (int) ($dados_antes['status'] ?? -1) : -1;
    $status_mudou = ((int) $status !== $status_anterior);

    $c->set_status($id_corrida, $status);
    if ($taxa) {
        $c->atualiza_taxa($id_corrida, $taxa);
    }
    if ($status_mudou) {
        $status_string = $c->status_string($status);
        $sh->salva_status($id_corrida, $status_string, 'App Motorista');
    }

    $dados_corrida = $c->get_corrida_id($id_corrida);
    if (!$dados_corrida) {
        echo 'no';
        exit;
    }

    $id_motorista = (int) ($dados_corrida['motorista_id'] ?? 0);
    $dados_motorista = $id_motorista > 0 ? $m->get_motorista($id_motorista) : null;

    if ($status_mudou && ($status == '4' || $status == 4) && $dados_motorista) {
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

    // Disponibilidade do motorista ao finalizar (crítico — antes de responder).
    if ($status_mudou && ($status == '4' || $status == 4) && $id_motorista > 0) {
        $m->atualiza_disponibilidade($id_motorista, 1);
    }

    // Responde o APP agora. As notificações (webhook/push) rodam DEPOIS, sem
    // travar o motorista mesmo se o n8n estiver lento (o webhook tem retry).
    echo 'ok';
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    if ($status_mudou) {
        // Limpeza do histórico do bot ao finalizar (não é aviso de status).
        try {
            $user_whatsapp = $dados_corrida['user_whatsapp'] ?? null;
            if ($user_whatsapp && (int) $status === 4) {
                $ubw->limpaMensagens(PATCH_LIMPA_MSG, $user_whatsapp);
            }
        } catch (Throwable $waErr) {
            error_log('[atualiza.php] limpaMensagens: ' . $waErr->getMessage());
        }

        // Push pro passageiro do APP (não é WhatsApp/IA).
        try {
            $id_cliente = $dados_corrida['cliente_id'] ?? null;
            if ($id_cliente && $dados_motorista) {
                $dados_cliente_push = $cl->get_cliente_id($id_cliente);
                if ($dados_cliente_push) {
                    $st = (int) $status;
                    if (in_array($st, [2, 3, 4, 5], true)) {
                        ExpoPush::notifyPassengerTripStatus($dados_cliente_push, $st, $dados_motorista['nome'] ?? 'Motorista', $id_corrida);
                    }
                }
            }
        } catch (Throwable $pushErr) {
            error_log('[atualiza.php] Push: ' . $pushErr->getMessage());
        }

        // Webhook do BOT/IA — a IA Sol manda a mensagem no WhatsApp.
        try {
            require_once __DIR__ . '/../classes/bot_webhook.php';
            $st = (int) $status;
            $mapa_webhook = [2 => 'arrived', 3 => 'started', 4 => 'completed'];
            if (isset($mapa_webhook[$st])) {
                $extra_webhook = ($st === 4) ? ['valor' => $dados_corrida['taxa'] ?? ''] : [];
                BotWebhook::notificarPassageiro($dados_corrida, $mapa_webhook[$st], $extra_webhook);
            }
        } catch (Throwable $whErr) {
            error_log('[atualiza.php] BotWebhook: ' . $whErr->getMessage());
        }
    }
    exit;
} catch (Throwable $e) {
    error_log('[atualiza.php] ' . $e->getMessage());
    http_response_code(500);
    echo 'erro';
}
