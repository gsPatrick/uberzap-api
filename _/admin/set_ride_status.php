<?php
// Ajuste direto de status de corrida (correcao de dados de teste) — sem efeitos
// colaterais (nao mexe em financeiro/WhatsApp). Protegido por secret.
// Uso: ?secret=abc1234&id=1582&status=4
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/corridas.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? $_POST['secret'] ?? '')) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Secret inválida.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$novo = (string) ($_GET['status'] ?? $_POST['status'] ?? '');
if ($id < 1 || $novo === '') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Informe id e status.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $c = new corridas();
    $c->set_status($id, $novo);
    $dados = $c->get_corrida_id($id);
    echo json_encode([
        'status' => 'ok',
        'id' => $id,
        'novo_status' => $dados['status'] ?? null,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
