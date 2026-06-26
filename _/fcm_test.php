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

$rideTeste = [
    'type' => 'ride_alert',
    'rideId' => 'TESTE',
    'channelId' => 'ride_alert',
    'taxa' => '12,34',
    'endereco_ini_txt' => 'TESTE - Rua do Embarque',
    'endereco_fim_txt' => 'TESTE - Av. do Destino',
    'nome_cliente' => 'TESTE',
    'nota_cliente' => '5',
    'km' => '3',
    'tempo' => '8',
];

// Envio direto a um token avulso.
$token = $_GET['token'] ?? ($_POST['token'] ?? '');
if ($token !== '') {
    $out['envio_token'] = FcmV1::sendData($token, $rideTeste);
}

// Diagnóstico por motorista: status online + token + envio real ao aparelho.
$mot = $_GET['mot'] ?? ($_POST['mot'] ?? '');
if ($mot !== '') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM motoristas WHERE id = :id');
    $stmt->bindValue(':id', $mot);
    $stmt->execute();
    $m = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($m) {
        $online = ($m['ativo'] ?? '') === '1' && ($m['online'] ?? '') === '1';
        $out['motorista'] = [
            'id' => $m['id'] ?? null,
            'online_para_receber' => $online,
            'ativo' => $m['ativo'] ?? null,
            'online' => $m['online'] ?? null,
            'cidade_id' => $m['cidade_id'] ?? null,
            'tem_fcm_token' => !empty($m['fcm_token']),
            'tem_expo_token' => !empty($m['id_signal']),
        ];
        if (!empty($m['fcm_token'])) {
            $out['envio_para_motorista'] = FcmV1::sendData($m['fcm_token'], $rideTeste);
        } else {
            $out['envio_para_motorista'] = 'SEM fcm_token (build antiga ou nao logou no build novo)';
        }
    } else {
        $out['motorista'] = 'nao encontrado';
    }
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
