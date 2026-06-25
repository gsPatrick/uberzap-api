<?php
/**
 * Migração one-shot: cria os índices de performance via a conexão PDO da API
 * (a VPS não tem o cliente `mysql`). Idempotente — só cria o índice se faltar.
 *
 * Uso (uma vez):
 *   https://SEU_DOMINIO/_/migrate_indices.php?secret=SEU_SECRET
 *
 * Segurança: protegido pelo mesmo secret das demais APIs. Pode (e deve) ser
 * apagado depois de rodar. Não altera dados — só adiciona índices.
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/bd/conexao.php';
require_once __DIR__ . '/classes/seguranca.php';
global $pdo;

$secret = $_GET['secret'] ?? $_POST['secret'] ?? '';
$s = new seguranca();
if (!$s->compare_secret($secret)) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'secret invalido'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'sem conexao PDO'], JSON_UNESCAPED_UNICODE);
    exit;
}

// [tabela, nome_do_indice, "colunas"]
$indices = [
    ['corridas',              'idx_corridas_cliente',     '`cliente_id`, `id`'],
    ['corridas',              'idx_corridas_motorista',   '`motorista_id`, `date`'],
    ['corridas',              'idx_corridas_cidade_stat', '`cidade_id`, `status`, `date`'],
    ['corridas',              'idx_corridas_whatsapp',    '`user_whatsapp`, `status`'],
    ['avaliacoes',            'idx_avaliacoes_corrida',   '`corrida_id`'],
    ['avaliacoes',            'idx_avaliacoes_motorista', '`motorista_id`'],
    ['transacoes',            'idx_transacoes_user',      '`user_id`, `id`'],
    ['transacoes_motoristas', 'idx_transacoes_mot_user',  '`user_id`, `id`'],
    ['msg',                   'idx_msg_corrida',          '`id_corrida`, `id`'],
    ['localizacao_corridas',  'idx_localizacao_corrida',  '`corrida_id`, `date`'],
    ['clientes',              'idx_clientes_telefone',    '`telefone`'],
    ['motoristas',            'idx_motoristas_cpf',       '`cpf`'],
    ['motoristas',            'idx_motoristas_online',    '`cidade_id`, `ativo`, `online`'],
];

$checkStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM information_schema.statistics
     WHERE table_schema = DATABASE() AND table_name = :t AND index_name = :i"
);

$resultado = [];
foreach ($indices as [$tabela, $indice, $colunas]) {
    try {
        $checkStmt->execute([':t' => $tabela, ':i' => $indice]);
        if ((int) $checkStmt->fetchColumn() > 0) {
            $resultado[] = "ja existe: {$indice}";
            continue;
        }
        $pdo->exec("CREATE INDEX `{$indice}` ON `{$tabela}` ({$colunas})");
        $resultado[] = "criado: {$indice}";
    } catch (Throwable $e) {
        $resultado[] = "ERRO {$indice}: " . $e->getMessage();
    }
}

echo json_encode(['status' => 'ok', 'resultado' => $resultado], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
