<?php
// Leitor do log de diagnóstico do WhatsApp (uso temporário). Protegido por secret.
// GET ?secret=abc1234  -> últimas linhas | ?secret=abc1234&clear=1 -> limpa
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';

$s = new seguranca();
if (!$s->compare_secret($_GET['secret'] ?? '')) {
    http_response_code(401);
    echo 'no_auth';
    exit;
}

$file = __DIR__ . '/uploads/wa_debug.log';

if (($_GET['clear'] ?? '') === '1') {
    @file_put_contents($file, '');
    echo 'limpo';
    exit;
}

if (!file_exists($file)) {
    echo '(log vazio — nenhum status atualizado ainda)';
    exit;
}

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$tail = array_slice($lines, -60);
echo implode("\n", $tail);
