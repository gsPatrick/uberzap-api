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
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_motorista = (int) ($_POST['id_motorista'] ?? 0);
$status = (int) ($_POST['status'] ?? 0);
$latitude = $_POST['latitude'] ?? '0';
$longitude = $_POST['longitude'] ?? '0';

if ($id_motorista < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Motorista inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$m = new Motoristas();
$m->atualiza_coordenadas($id_motorista, $latitude, $longitude);
$m->atualiza_disponibilidade($id_motorista, $status);

error_log("GPS Update - Motorista: $id_motorista | Lat: $latitude | Lng: $longitude | Status: $status");

echo json_encode([
    'status' => 'ok',
    'online' => $status,
], JSON_UNESCAPED_UNICODE);

// SEM CRON: aproveita o tráfego de GPS dos motoristas online (a cada poucos
// segundos) pra varrer o banco e disparar o push de corridas novas de QUALQUER
// origem (inclusive API antiga). Resposta já foi enviada — não atrasa o GPS.
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
try {
    require_once __DIR__ . '/../classes/ride_dispatch.php';
    RideDispatch::scanAndDispatch(2);
} catch (\Throwable $e) {
    // best-effort: nunca afeta o update de localização
}
