<?php
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/status_historico.php';
require_once __DIR__ . '/../classes/motoristas.php';
require_once __DIR__ . '/../classes/transacoes_motoristas.php';

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
$km = (string) ($_POST['km'] ?? '0');
$endereco_fim = (string) ($_POST['endereco_fim'] ?? 'Destino não informado');

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

    $dados_motorista = $m->get_motorista($id_motorista);
    if (!$dados_motorista) {
        echo 'no';
        exit;
    }

    $taxa_porcentagem = (float) str_replace(',', '.', (string) ($dados_motorista['taxa'] ?? 0));
    $saldo_motorista = (float) str_replace(',', '.', (string) ($dados_motorista['saldo'] ?? 0));
    $taxa_motorista = $taxa_porcentagem * (float) $taxa / 100;
    $novo_saldo = number_format($saldo_motorista - $taxa_motorista, 2, '.', '');
    $m->atualiza_saldo($id_motorista, $novo_saldo);

    $taxa_motorista_fmt = number_format($taxa_motorista, 2, ',', '');
    $tm->insereTransacao($id_motorista, 'N/A', $taxa_motorista_fmt, 'DEBITO TAXIMETRO', 'CONCLUIDO');

    $c->update_taximetro($id_corrida, $taxa, $tempo, $km, $endereco_fim);
    $c->set_status($id_corrida, 4);
    $sh->salva_status($id_corrida, 'Taxímetro finalizado', 'App Motorista');
    echo 'ok';
} catch (Throwable $e) {
    error_log('[atualiza_taxi.php] ' . $e->getMessage());
    http_response_code(500);
    echo 'erro';
}
