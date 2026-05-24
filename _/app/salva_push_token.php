<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../bd/normalize.php';
require_once __DIR__ . '/../classes/clientes.php';

$telefone = ubezap_post_digits(['telefone', 'cpf', 'login']);
$senha = (string) ($_POST['senha'] ?? '');
$id_signal = trim((string) ($_POST['id_signal'] ?? ''));

if ($telefone === '' || $senha === '' || $id_signal === '') {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados incompletos'], JSON_UNESCAPED_UNICODE);
    exit;
}

$salt = 'anjdsn5s141d5';
$senhaHash = md5($senha . $salt);

$c = new Clientes();
$cliente = $c->login($telefone, $senha);
if (!$cliente) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Login inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$c->atualiza_push_token($cliente['id'], $id_signal);
echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
