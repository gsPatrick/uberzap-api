<?php
// Diagnóstico do FCM v1 (protegido por secret).
//   confere se a service account foi lida:  /_/fcm_test.php?secret=SEU_SECRET
//   testa envio a um token real:            /_/fcm_test.php?secret=SEU_SECRET&token=FCM_TOKEN
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/bd/conexao.php';
require_once __DIR__ . '/classes/seguranca.php';
require_once __DIR__ . '/classes/fcm_v1.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? ($_POST['secret'] ?? ''))) {
    http_response_code(401);
    echo json_encode(['status' => 'erro']);
    exit;
}

$out = [
    'service_account_configurada' => FcmV1::isConfigured(),
    'project_id' => FcmV1::projectId(),
];

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
if ($token !== '') {
    $out['envio_teste'] = FcmV1::sendData($token, [
        'type' => 'ride_alert',
        'rideId' => 'TEST',
        'channelId' => 'ride_alert',
        'taxa' => '10,00',
        'endereco_ini_txt' => 'Teste',
        'endereco_fim_txt' => 'Teste',
        'nome_cliente' => 'Teste',
    ]);
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
