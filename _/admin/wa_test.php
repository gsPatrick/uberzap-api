<?php
// Teste de envio de WhatsApp pela instância configurada (config.php).
// Uso: ?secret=abc1234&phone=5566999999999&msg=Ola%20teste
// Retorna a resposta crua do w-api (pra ver se conectou e enviou).
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd/config.php';
require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/w_api.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? $_POST['secret'] ?? '')) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Secret inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$phone = preg_replace('/\D/', '', (string) ($_GET['phone'] ?? $_POST['phone'] ?? ''));
$msg = (string) ($_GET['msg'] ?? $_POST['msg'] ?? 'Mensagem de teste UbeZap');

if ($phone === '') {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Informe phone. Ex.: ?secret=abc1234&phone=5566999999999&msg=Ola',
        'instancia' => W_API_ID,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $wapi = new w_api(W_API_TOKEN, W_API_ID);
    $envio = $wapi->enviarMensagem($phone, $msg);
    echo json_encode([
        'status' => 'ok',
        'instancia' => W_API_ID,
        'phone' => $phone,
        'msg' => $msg,
        'resposta_wapi' => $envio,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage(), 'instancia' => W_API_ID], JSON_UNESCAPED_UNICODE);
}
