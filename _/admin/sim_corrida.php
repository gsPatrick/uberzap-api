<?php
// Simula a sequência de mensagens de uma corrida para um número (demo/teste).
// Dispara: aceite -> chegou -> iniciou -> finalizou, pela instância conectada.
// Uso: ?secret=abc1234&phone=5571983141335
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd/config.php';
require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/w_api.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? '')) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Secret inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$phone = preg_replace('/\D/', '', (string) ($_GET['phone'] ?? ''));
if ($phone === '') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Informe phone (ex.: 5571983141335).'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome = $_GET['nome'] ?? 'Marcelo Uber';
$placa = $_GET['placa'] ?? 'QCK1B81';
$veiculo = $_GET['veiculo'] ?? 'Ônix Sedan branco';
$valor = $_GET['valor'] ?? '20,98';

$mensagens = [
    "🆗 O motorista $nome aceitou a sua corrida e está a caminho!\nPlaca: $placa\nVeículo: $veiculo",
    "📌 O motorista chegou ao local de embarque. Placa do veículo: $placa",
    "🚗 A corrida foi iniciada. Boa viagem!",
    "✅ A corrida foi finalizada.\nValor da corrida: R$ $valor\n\nAgradecemos a preferência!",
];

$wapi = new w_api(W_API_TOKEN, W_API_ID);
$resultados = [];
foreach ($mensagens as $i => $msg) {
    try {
        $envio = $wapi->enviarMensagem($phone, $msg);
        $resultados[] = ['passo' => $i + 1, 'enviado' => $envio];
    } catch (Throwable $e) {
        $resultados[] = ['passo' => $i + 1, 'erro' => $e->getMessage()];
    }
    if ($i < count($mensagens) - 1) {
        sleep(3); // espaça as mensagens para chegarem em ordem
    }
}

echo json_encode([
    'status' => 'ok',
    'instancia' => W_API_ID,
    'phone' => $phone,
    'resultados' => $resultados,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
