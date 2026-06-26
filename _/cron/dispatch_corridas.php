<?php
/**
 * CRON DISPATCHER — o banco é o gatilho.
 *
 * Vigia a tabela `corridas` e dispara o push pra QUALQUER corrida nova, de
 * qualquer origem (app/API antiga, WhatsApp, painel, app/API nova). O dedup é
 * garantido pelo RideDispatch (PK em corridas_dispatch) — nunca duplica.
 *
 * Roda como cron de 1 MINUTO; internamente faz polling a cada 2s por ~55s, então
 * a latência real é ~2s. Um lock impede 2 execuções ao mesmo tempo.
 *
 * Uso:
 *   CLI : php /var/www/html/_/cron/dispatch_corridas.php SEU_SECRET
 *   web : curl "https://.../_/cron/dispatch_corridas.php?secret=SEU_SECRET"
 *   debug 1 ciclo: ... ?once=1   (ou argumento "once")
 */
@set_time_limit(75);
@ignore_user_abort(true);

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/ride_dispatch.php';
require_once __DIR__ . '/../classes/uzlog.php';

global $pdo;

// --- auth (secret por argv no CLI ou ?secret= no web) ---
if (PHP_SAPI === 'cli') {
    $secret = $argv[1] ?? '';
    $once = in_array('once', $argv, true);
} else {
    $secret = $_GET['secret'] ?? '';
    $once = isset($_GET['once']);
}
$sec = new seguranca();
if (!$sec->compare_secret($secret)) {
    http_response_code(401);
    exit("erro: secret invalido\n");
}

// --- lock anti-sobreposição ---
$fp = @fopen(sys_get_temp_dir() . '/uz_dispatch.lock', 'c');
if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    exit("ocupado (outro ciclo rodando)\n");
}

RideDispatch::ensureTable();

$janela = 55;   // segundos de loop (cron de 1 min)
$passo = 2;     // polling a cada 2s
$started = time();
$total = 0;

do {
    try {
        $rows = $pdo->query(
            "SELECT c.* FROM corridas c
             LEFT JOIN corridas_dispatch d ON d.corrida_id = c.id
             WHERE d.corrida_id IS NULL
               AND c.motorista_id = 0
               AND c.status = 0
             ORDER BY c.id ASC
             LIMIT 30"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $corrida) {
            $r = RideDispatch::dispatch($corrida, 'cron');
            if ($r >= 0) {
                $total++;
            }
        }
    } catch (\Throwable $e) {
        uzlog('[dispatch-cron] ERRO no ciclo: ' . $e->getMessage());
    }

    if (!$once) {
        sleep($passo);
    }
} while (!$once && (time() - $started) < $janela);

flock($fp, LOCK_UN);
fclose($fp);
echo "ok dispatched=$total\n";
