<?php
// Diagnóstico de dispatch de corridas (temporário). ?secret=abc1234&cidade=2
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? '')) {
    http_response_code(401);
    echo 'no_auth';
    exit;
}

$cidade = (int) ($_GET['cidade'] ?? 2);

$out = [];
try {
    $out['db_now'] = $pdo->query("SELECT NOW()")->fetchColumn();
    $out['db_timezone'] = $pdo->query("SELECT @@session.time_zone, @@global.time_zone")->fetch(PDO::FETCH_ASSOC);

    // Últimas 6 corridas (qualquer status)
    $q = $pdo->query("SELECT id, status, cidade_id, categoria_id, date FROM corridas ORDER BY id DESC LIMIT 6");
    $out['ultimas_corridas'] = $q->fetchAll(PDO::FETCH_ASSOC);

    // Corridas status 0 na cidade (sem filtro de tempo)
    $st = $pdo->prepare("SELECT id, status, cidade_id, categoria_id, date FROM corridas WHERE cidade_id = :c AND status = '0' ORDER BY id DESC LIMIT 6");
    $st->execute([':c' => $cidade]);
    $out['pendentes_cidade'] = $st->fetchAll(PDO::FETCH_ASSOC);

    // A query EXATA do dispatch (com o filtro de 2h)
    $st2 = $pdo->prepare("SELECT id, status, cidade_id, date FROM corridas WHERE cidade_id = :c AND status = '0' AND date >= DATE_SUB(NOW(), INTERVAL 2 HOUR) ORDER BY date ASC");
    $st2->execute([':c' => $cidade]);
    $out['dispatch_com_filtro_2h'] = $st2->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $out['erro'] = $e->getMessage();
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
