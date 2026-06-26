<?php
// Adiciona a coluna de controle `push_enviado` na tabela corridas (idempotente)
// e mostra as colunas da tabela. Protegido por secret.
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/bd/conexao.php';
require_once __DIR__ . '/classes/seguranca.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? '')) {
    http_response_code(401);
    echo json_encode(['status' => 'erro']);
    exit;
}

global $pdo;
$out = [];

$cols = $pdo->query('SHOW COLUMNS FROM corridas')->fetchAll(PDO::FETCH_ASSOC);
$out['colunas'] = array_map(function ($c) {
    return $c['Field'] . ' (' . $c['Type'] . ')';
}, $cols);
$names = array_column($cols, 'Field');

if (!in_array('push_enviado', $names, true)) {
    $pdo->exec('ALTER TABLE corridas ADD COLUMN push_enviado TINYINT NOT NULL DEFAULT 0');
    // Marca TODAS as corridas existentes como já enviadas — evita o cron
    // disparar push pra corridas antigas no primeiro ciclo.
    $pdo->exec('UPDATE corridas SET push_enviado = 1');
    $out['migracao'] = 'coluna push_enviado criada + corridas existentes marcadas como enviadas';
} else {
    $out['migracao'] = 'coluna push_enviado ja existe';
}

// Amostra das últimas corridas (pra entender status/data).
$out['ultimas'] = $pdo->query('SELECT id, motorista_id, status, push_enviado FROM corridas ORDER BY id DESC LIMIT 5')
    ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
