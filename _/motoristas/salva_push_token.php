<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/motoristas.php';

$secret_key = $_POST['secret'] ?? '';
$s = new seguranca();
if (!$s->compare_secret($secret_key)) {
    http_response_code(401);
    echo json_encode(['status' => 'erro'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_motorista = (int) ($_POST['id_motorista'] ?? 0);
$id_signal = trim((string) ($_POST['id_signal'] ?? ''));

if ($id_motorista < 1 || $id_signal === '') {
    http_response_code(400);
    echo json_encode(['status' => 'erro'], JSON_UNESCAPED_UNICODE);
    exit;
}

$m = new Motoristas();
$m->atualiza_push_token($id_motorista, $id_signal);
echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
