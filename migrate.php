<?php
/**
 * ============================================================================
 *  UbeZap - Migração de schema SEGURA (somente aditiva)
 * ============================================================================
 *
 *  O QUE ELE FAZ:
 *    - Lê o schema-alvo de um dump .sql (homologação) e compara com o banco
 *      de PRODUÇÃO já em uso.
 *    - Cria APENAS o que está faltando:
 *        * tabelas inexistentes  -> CREATE TABLE IF NOT EXISTS
 *        * colunas faltantes     -> ALTER TABLE ... ADD COLUMN
 *        * índices faltantes     -> ALTER TABLE ... ADD <índice>
 *
 *  O QUE ELE NUNCA FAZ (por design):
 *    - NÃO faz DROP TABLE / DROP COLUMN
 *    - NÃO faz DELETE / TRUNCATE
 *    - NÃO faz MODIFY/CHANGE em coluna existente (não altera tipo de dado)
 *    - NÃO executa INSERT de dados (ignora todo o conteúdo do dump)
 *    - NÃO toca em tabelas que existam só na produção
 *  => Nenhum dado de usuário é apagado ou alterado.
 *
 *  COMO USAR (no SERVIDOR, onde o MySQL é acessível):
 *
 *    1) BACKUP PRIMEIRO (obrigatório). O comando é impresso ao rodar o dry-run.
 *
 *    2) Dry-run (NÃO altera nada, só mostra o plano):
 *         php migrate.php
 *
 *    3) Validar o parser sem banco (opcional):
 *         php migrate.php --selftest
 *
 *    4) Aplicar de fato (depois de revisar o dry-run e ter o backup):
 *         php migrate.php --apply
 *
 *    Caminho do dump (se não estiver ao lado): --schema=/caminho/arquivo.sql
 *
 *    Via navegador (se não tiver SSH): suba o arquivo e acesse
 *         .../migrate.php                 (dry-run)
 *         .../migrate.php?apply=1&confirm=APLICAR   (aplica)
 * ============================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
@set_time_limit(0);

$IS_CLI = (php_sapi_name() === 'cli');

/* ---------- Opções ---------- */
$opt = [
    'apply'    => false,
    'selftest' => false,
    'schema'   => null,
    'indexes'  => true,   // adicionar índices faltantes (--no-indexes desliga)
    'emit'     => null,   // gera arquivo só-DDL (sem dados) e sai
];

if ($IS_CLI) {
    foreach (array_slice($argv, 1) as $a) {
        if ($a === '--apply')          $opt['apply'] = true;
        elseif ($a === '--selftest')   $opt['selftest'] = true;
        elseif ($a === '--no-indexes') $opt['indexes'] = false;
        elseif (strpos($a, '--schema=') === 0) $opt['schema'] = substr($a, 9);
        elseif (strpos($a, '--emit=')   === 0) $opt['emit']   = substr($a, 7);
    }
} else {
    header('Content-Type: text/plain; charset=utf-8');
    $opt['apply']   = (isset($_GET['apply']) && $_GET['apply'] == '1'
                       && isset($_GET['confirm']) && $_GET['confirm'] === 'APLICAR');
    $opt['indexes'] = !isset($_GET['no_indexes']);
    if (!empty($_GET['schema'])) $opt['schema'] = $_GET['schema'];
}

function out($s = '') { echo $s . "\n"; }

/* ---------- Localizar o dump (schema-alvo) ---------- */
function localizar_schema($override)
{
    if ($override) return $override;
    $cands = [
        __DIR__ . '/schema_alvo.sql',
        __DIR__ . '/uberzapapp_transporte.sql',
        __DIR__ . '/../uberzapapp_transporte.sql',
        __DIR__ . '/_/uberzapapp_transporte.sql',
        dirname(__DIR__) . '/uberzapapp_transporte.sql',
    ];
    foreach ($cands as $c) if (is_file($c)) return $c;
    return null;
}

/* ----------------------------------------------------------------------------
 * Parser do dump (formato phpMyAdmin):
 *   - CREATE TABLE define as COLUNAS (sem PK/AI inline).
 *   - Blocos "ALTER TABLE ... ADD PRIMARY KEY/ADD KEY/ADD UNIQUE" no fim do
 *     arquivo definem os ÍNDICES.
 *   - Blocos "ALTER TABLE ... MODIFY `id` ... AUTO_INCREMENT" definem o AI.
 * Cada tabela vira: ['create','cols'=>[nome=>def],'idx'=>[nome=>clausula],
 *                     'pk'=>clausula|null,'auto'=>clausula|null]
 * -------------------------------------------------------------------------- */
function parse_schema($sqlPath)
{
    $linhas = file($sqlPath, FILE_IGNORE_NEW_LINES);
    if ($linhas === false) {
        throw new RuntimeException("Não consegui ler o schema: $sqlPath");
    }

    $tabelas = [];
    $ordem   = [];

    /* ---- Passo 1: CREATE TABLE -> colunas (aceita "IF NOT EXISTS") ---- */
    $atual = null; $buf = [];
    foreach ($linhas as $ln) {
        if ($atual === null) {
            if (preg_match('/^\s*CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?([A-Za-z0-9_]+)`?\s*\(/i', $ln, $m)) {
                $atual = $m[1]; $buf = [$ln];
            }
            continue;
        }
        $buf[] = $ln;
        if (preg_match('/^\s*\)\s*(ENGINE|;|$)/i', $ln)) {
            $full = implode("\n", $buf);
            $cols = [];
            $inner = array_slice($buf, 1, count($buf) - 2);
            foreach ($inner as $raw) {
                $t = rtrim(trim($raw), ',');
                if ($t !== '' && preg_match('/^`([A-Za-z0-9_]+)`\s+/', $t, $mc)) {
                    $cols[$mc[1]] = $t;
                }
            }
            // idempotente: só adiciona IF NOT EXISTS se ainda não tiver
            if (preg_match('/^\s*CREATE TABLE\s+IF NOT EXISTS/i', $full)) {
                $createSafe = $full;
            } else {
                $createSafe = preg_replace('/^\s*CREATE TABLE\s+/i', 'CREATE TABLE IF NOT EXISTS ', $full, 1);
            }
            $tabelas[$atual] = [
                'create' => $createSafe, 'cols' => $cols,
                'idx' => [], 'pk' => null, 'auto' => null,
            ];
            $ordem[] = $atual;
            $atual = null; $buf = [];
        }
    }

    /* ---- Passo 2: ALTER TABLE -> índices, PK e AUTO_INCREMENT ----
     * Acumula a instrução ALTER inteira (mono ou multilinha) até o ';' e
     * extrai as cláusulas por regex (robusto aos dois formatos). */
    $flush = function ($tabela, $body) use (&$tabelas) {
        if (!isset($tabelas[$tabela])) return;
        if (preg_match('/ADD\s+PRIMARY\s+KEY\s*\([^)]*\)/i', $body, $m)) {
            $tabelas[$tabela]['pk'] = trim($m[0]);
            $tabelas[$tabela]['idx']['PRIMARY'] = trim($m[0]);
        }
        if (preg_match_all('/ADD\s+(?:UNIQUE\s+|FULLTEXT\s+|SPATIAL\s+)?KEY\s+`([A-Za-z0-9_]+)`\s*\([^)]*\)/i', $body, $ms, PREG_SET_ORDER)) {
            foreach ($ms as $mm) $tabelas[$tabela]['idx'][$mm[1]] = trim($mm[0]);
        }
        if (preg_match('/MODIFY\s+`?[A-Za-z0-9_]+`?[^,;]*AUTO_INCREMENT/i', $body, $m)) {
            $tabelas[$tabela]['auto'] = trim($m[0]);   // sem o contador ", AUTO_INCREMENT=N"
        }
    };

    $alvo = null; $abuf = '';
    foreach ($linhas as $ln) {
        if ($alvo === null) {
            if (preg_match('/^\s*ALTER TABLE\s+`?([A-Za-z0-9_]+)`?(.*)$/i', $ln, $m)) {
                $alvo = $m[1]; $abuf = $m[2] . ' ';
                if (strpos($ln, ';') !== false) { $flush($alvo, $abuf); $alvo = null; $abuf = ''; }
            }
            continue;
        }
        $abuf .= $ln . ' ';
        if (strpos($ln, ';') !== false) { $flush($alvo, $abuf); $alvo = null; $abuf = ''; }
    }

    return ['tabelas' => $tabelas, 'ordem' => $ordem];
}

/* ============================ EXECUÇÃO ============================ */

$schemaPath = localizar_schema($opt['schema']);
out("=== UbeZap • Migração de schema (somente aditiva) ===");
out("Modo: " . ($opt['selftest'] ? "SELFTEST (sem banco)" : ($opt['apply'] ? "APLICAR" : "DRY-RUN (nada será alterado)")));
out("Schema-alvo (dump): " . ($schemaPath ?: "NÃO ENCONTRADO"));
out("");

if (!$schemaPath) {
    out("ERRO: não localizei o arquivo .sql do schema. Use --schema=/caminho/arquivo.sql");
    exit(1);
}

$parsed  = parse_schema($schemaPath);
$tabelas = $parsed['tabelas'];
$ordem   = $parsed['ordem'];

out("Tabelas no schema-alvo: " . count($ordem));
foreach ($ordem as $t) {
    out(sprintf("  - %-26s %2d colunas, %2d índices, PK:%s, AUTO_INC:%s",
        $t, count($tabelas[$t]['cols']), count($tabelas[$t]['idx']),
        $tabelas[$t]['pk'] ? 'sim' : 'NÃO',
        $tabelas[$t]['auto'] ? 'sim' : 'não'));
}
out("");

if ($opt['selftest']) {
    out("Selftest OK — parser leu o schema sem erros. (Nenhuma conexão feita.)");
    exit(0);
}

if ($opt['emit']) {
    $ddl  = "-- UbeZap • Schema-alvo SOMENTE DDL (sem dados). Gerado por migrate.php --emit\n";
    $ddl .= "-- Seguro para subir no servidor: não contém nenhum INSERT/dado de usuário.\n\n";
    foreach ($ordem as $t) {
        $d = $tabelas[$t];
        $ddl .= "-- ---- $t ----\n" . $d['create'] . "\n";
        if ($d['pk'])   $ddl .= "ALTER TABLE `$t` " . $d['pk'] . ";\n";
        foreach ($d['idx'] as $n => $c) { if ($n === 'PRIMARY') continue; $ddl .= "ALTER TABLE `$t` $c;\n"; }
        if ($d['auto']) $ddl .= "ALTER TABLE `$t` " . $d['auto'] . ";\n";
        $ddl .= "\n";
    }
    if (file_put_contents($opt['emit'], $ddl) === false) {
        out("ERRO: não consegui escrever em " . $opt['emit']);
        exit(1);
    }
    out("Gerado schema só-DDL (" . count($ordem) . " tabelas) em: " . $opt['emit']);
    out("Esse arquivo NÃO contém dados — pode subir junto do migrate.php.");
    exit(0);
}

/* ---------- Conexão (usa as credenciais de produção do conexao.php) ---------- */
require_once __DIR__ . '/_/bd/conexao.php';
/** @var PDO $pdo */
$pdo = ubezap_get_pdo();

$dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
$hostInfo = @$pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
out("Conectado ao banco: '$dbName'  ($hostInfo)");
out("");
out(">>> FAÇA BACKUP ANTES DE APLICAR. Exemplo:");
out("    mysqldump -h " . ($GLOBALS['hostname'] ?? 'HOST') . " -P " . ($GLOBALS['port'] ?? 3306) .
    " -u " . ($GLOBALS['user'] ?? 'USER') . " -p " . $dbName .
    " > backup_" . $dbName . "_PRE_MIGRACAO.sql");
out("");

/* ---------- Schema vivo ---------- */
$liveTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$liveTables = array_map('strtolower', $liveTables);
$liveTablesSet = array_flip($liveTables);

$plano = []; // ['sql'=>..., 'tipo'=>...]

foreach ($ordem as $tabela) {
    $def = $tabelas[$tabela];

    if (!isset($liveTablesSet[strtolower($tabela)])) {
        // tabela nova: CREATE + PRIMARY KEY + índices + AUTO_INCREMENT
        $plano[] = ['tipo' => 'CREATE TABLE', 'tabela' => $tabela, 'sql' => $def['create']];
        if ($def['pk']) {
            $plano[] = ['tipo' => 'ADD PK', 'tabela' => $tabela, 'sql' => "ALTER TABLE `$tabela` " . $def['pk']];
        }
        foreach ($def['idx'] as $idxNome => $idxDef) {
            if ($idxNome === 'PRIMARY') continue;
            $plano[] = ['tipo' => 'ADD INDEX', 'tabela' => $tabela, 'sql' => "ALTER TABLE `$tabela` " . $idxDef];
        }
        if ($def['auto']) {
            $plano[] = ['tipo' => 'AUTO_INCR', 'tabela' => $tabela, 'sql' => "ALTER TABLE `$tabela` " . $def['auto']];
        }
        continue;
    }

    // tabela existe -> achar colunas faltantes
    $liveCols = $pdo->query("SHOW COLUMNS FROM `$tabela`")->fetchAll(PDO::FETCH_COLUMN);
    $liveColsSet = array_change_key_case(array_flip($liveCols), CASE_LOWER);

    $prev = null;
    foreach ($def['cols'] as $colNome => $colDef) {
        if (!isset($liveColsSet[strtolower($colNome)])) {
            $pos = $prev ? "AFTER `$prev`" : "FIRST";
            $plano[] = [
                'tipo'   => 'ADD COLUMN',
                'tabela' => $tabela,
                'sql'    => "ALTER TABLE `$tabela` ADD COLUMN $colDef $pos",
            ];
        }
        $prev = $colNome;
    }

    // índices faltantes
    if ($opt['indexes']) {
        $liveIdx = [];
        foreach ($pdo->query("SHOW INDEX FROM `$tabela`")->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $liveIdx[strtolower($r['Key_name'])] = true;
        }
        foreach ($def['idx'] as $idxNome => $idxDef) {
            $cmp = ($idxNome === 'PRIMARY') ? 'primary' : strtolower($idxNome);
            if (!isset($liveIdx[$cmp])) {
                $plano[] = [
                    'tipo'   => ($idxNome === 'PRIMARY' ? 'ADD PK' : 'ADD INDEX'),
                    'tabela' => $tabela,
                    'sql'    => "ALTER TABLE `$tabela` " . $idxDef,
                ];
            }
        }
    }
}

/* ---------- Mostrar plano ---------- */
out(str_repeat('=', 70));
if (!$plano) {
    out("Nada a fazer — a produção já tem todas as tabelas/colunas/índices do alvo.");
    exit(0);
}
out("PLANO (" . count($plano) . " alteração(ões) ADITIVA(S)):");
out(str_repeat('=', 70));
foreach ($plano as $i => $p) {
    out(sprintf("[%02d] %-12s %s", $i + 1, $p['tipo'], $p['tabela']));
    out("     " . str_replace("\n", "\n     ", $p['sql']) . ";");
    out("");
}

if (!$opt['apply']) {
    out(str_repeat('=', 70));
    out("DRY-RUN: nada foi alterado.");
    out("Para aplicar (após backup):  " . ($IS_CLI ? "php migrate.php --apply" : "?apply=1&confirm=APLICAR"));
    exit(0);
}

/* ---------- Aplicar ---------- */
out(str_repeat('=', 70));
out("APLICANDO...");
out(str_repeat('=', 70));
$ok = 0; $falhas = 0;
foreach ($plano as $i => $p) {
    try {
        $pdo->exec($p['sql']);
        out(sprintf("[%02d] OK    %s %s", $i + 1, $p['tipo'], $p['tabela']));
        $ok++;
    } catch (Throwable $e) {
        out(sprintf("[%02d] FALHA %s %s -> %s", $i + 1, $p['tipo'], $p['tabela'], $e->getMessage()));
        $falhas++;
        // continua: cada DDL é independente; nada destrutivo é executado.
    }
}
out("");
out(str_repeat('=', 70));
out("Concluído. Sucesso: $ok | Falhas: $falhas");
out("Nenhuma operação destrutiva foi executada (apenas CREATE/ADD).");
exit($falhas ? 2 : 0);
