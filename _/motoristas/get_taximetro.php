<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/taximetro.php';

$s = new seguranca();
if (!$s->compare_secret($_POST['secret'] ?? ($_GET['secret'] ?? ''))) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cidade_id = (int) ($_POST['cidade_id'] ?? ($_GET['cidade_id'] ?? 0));
if ($cidade_id < 1) {
    $cidade_id = 1;
}

$t = new Taximetro();
$tx = $t->getByCidadeId($cidade_id);

// Tarifas definidas no PAINEL (tabela taximetro). tx_minima = bandeirada/mínima.
echo json_encode([
    'tx_minima' => $tx ? str_replace(',', '.', (string) $tx['tx_minima']) : '0',
    'tx_minuto' => $tx ? str_replace(',', '.', (string) $tx['tx_minuto']) : '0',
    'tx_km'     => $tx ? str_replace(',', '.', (string) $tx['tx_km'])     : '0',
], JSON_UNESCAPED_UNICODE);
