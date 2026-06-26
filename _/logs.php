<?php
// Visualizador do log da API (protegido por secret).
//   ver últimas linhas:  /_/logs.php?secret=SEU_SECRET&n=100
//   limpar o log:        /_/logs.php?secret=SEU_SECRET&clear=1
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/classes/seguranca.php';

$s = new seguranca();
$secret = $_GET['secret'] ?? ($_POST['secret'] ?? '');
if (!$s->compare_secret($secret)) {
    http_response_code(401);
    echo "nao autorizado\n";
    exit;
}

$file = __DIR__ . '/logs/ubezap.log';

if (isset($_GET['clear'])) {
    @file_put_contents($file, '');
    echo "log limpo\n";
    exit;
}

if (!is_file($file)) {
    echo "(sem log ainda)\n";
    exit;
}

$n = (int) ($_GET['n'] ?? 200);
if ($n < 1) { $n = 200; }
$lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    echo "(vazio)\n";
    exit;
}
echo implode("\n", array_slice($lines, -$n)) . "\n";
