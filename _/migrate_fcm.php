<?php
// Adiciona a coluna motoristas.fcm_token (idempotente). Rodar 1x:
//   /_/migrate_fcm.php?secret=SEU_SECRET
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/bd/conexao.php';
require_once __DIR__ . '/classes/seguranca.php';
global $pdo;

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? ($_POST['secret'] ?? ''))) {
    http_response_code(401);
    echo json_encode(['status' => 'erro'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $check = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = 'motoristas' AND column_name = 'fcm_token'"
    );
    $check->execute();
    if ((int) $check->fetchColumn() > 0) {
        echo json_encode(['status' => 'ok', 'msg' => 'coluna fcm_token ja existe'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $pdo->exec("ALTER TABLE `motoristas` ADD COLUMN `fcm_token` VARCHAR(255) NULL DEFAULT NULL");
    echo json_encode(['status' => 'ok', 'msg' => 'coluna fcm_token criada'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'msg' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
