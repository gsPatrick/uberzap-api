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
$fcm_token = trim((string) ($_POST['fcm_token'] ?? ''));

if ($id_motorista < 1 || $fcm_token === '') {
    http_response_code(400);
    echo json_encode(['status' => 'erro'], JSON_UNESCAPED_UNICODE);
    exit;
}

$m = new Motoristas();
$m->atualiza_fcm_token($id_motorista, $fcm_token);

require_once __DIR__ . '/../classes/uzlog.php';
uzlog("[fcm] motorista #$id_motorista salvou fcm_token=" . substr($fcm_token, 0, 24) . "...");

echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
