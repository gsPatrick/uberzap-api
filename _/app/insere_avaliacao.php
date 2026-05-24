<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/clientes.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/avaliacoes.php';

$c = new Clientes();
$m = new corridas();
$a = new avaliacoes();

$senha = (string) ($_POST['senha'] ?? '');
$telefone = (string) ($_POST['telefone'] ?? '');
$corrida_id = (int) ($_POST['corrida_id'] ?? 0);
$nota = (int) ($_POST['nota'] ?? 0);
$comentario = trim((string) ($_POST['comentario'] ?? ''));

if ($telefone === '' || $senha === '') {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Telefone e senha são obrigatórios'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cliente = $c->login($telefone, $senha);
if (!$cliente) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Login inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($corrida_id < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID da corrida inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($nota < 1 || $nota > 5) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nota deve ser entre 1 e 5'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($a->verifica_avaliacao($corrida_id)) {
    echo json_encode(['status' => 'ok', 'mensagem' => 'Corrida já avaliada'], JSON_UNESCAPED_UNICODE);
    exit;
}

$corrida = $m->get_corrida_id($corrida_id);
if (!$corrida || (int) ($corrida['cliente_id'] ?? 0) !== (int) $cliente['id']) {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Corrida não encontrada para este passageiro'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ((int) ($corrida['status'] ?? 0) !== 4) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Só é possível avaliar corridas finalizadas'], JSON_UNESCAPED_UNICODE);
    exit;
}

$motorista_id = (int) ($corrida['motorista_id'] ?? 0);
if ($motorista_id < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Corrida sem motorista associado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$a->insere((int) $cliente['id'], $motorista_id, $nota, $comentario, $corrida_id);
echo json_encode(['status' => 'ok', 'mensagem' => 'Avaliação inserida com sucesso'], JSON_UNESCAPED_UNICODE);
