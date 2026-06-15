<?php
/**
 * SCRIPT DE USO ÚNICO — Reset em massa da senha de TODOS os motoristas.
 *
 * Define a senha de todos os motoristas para "mudarasenhaaposlogin"
 * (hash md5(senha + salt), o mesmo formato usado em motoristas/login.php).
 *
 * SEGURANÇA:
 *  - Exige a secret do sistema (?secret=abc1234) para rodar.
 *  - Exige confirm=SIM para evitar execução acidental.
 *  - Faz BACKUP automático (tabela motoristas_senha_backup) ANTES do update.
 *
 * COMO USAR (1x):
 *   https://SEU_DOMINIO/_/admin/reset_senhas_motoristas.php?secret=abc1234&confirm=SIM
 *
 * APAGUE ESTE ARQUIVO logo após rodar.
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bd/conexao.php';   // $pdo + $secret (produção, hardcoded)
require_once __DIR__ . '/../classes/seguranca.php';

$NOVA_SENHA = 'mudarasenhaaposlogin';
$SALT = 'anjdsn5s141d5';                       // mesmo salt de motoristas/login.php
$HASH = md5($NOVA_SENHA . $SALT);

try {
    // 1) Autenticação pela secret do sistema
    $secret_in = $_GET['secret'] ?? $_POST['secret'] ?? '';
    $s = new seguranca();
    if (!$s->compare_secret($secret_in)) {
        http_response_code(401);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Secret inválida.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2) Confirmação explícita
    $confirm = $_GET['confirm'] ?? $_POST['confirm'] ?? '';
    if (strtoupper(trim($confirm)) !== 'SIM') {
        $total = (int) $pdo->query("SELECT COUNT(*) FROM motoristas")->fetchColumn();
        echo json_encode([
            'status' => 'pendente',
            'mensagem' => "Pré-visualização: $total motoristas seriam atualizados. Para executar, adicione &confirm=SIM na URL.",
            'hash_a_aplicar' => $HASH,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 3) BACKUP automático da coluna senha (mantém histórico com timestamp)
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS motoristas_senha_backup (
            id INT, cpf VARCHAR(40), senha VARCHAR(255), backup_at DATETIME
        )"
    );
    $pdo->exec(
        "INSERT INTO motoristas_senha_backup (id, cpf, senha, backup_at)
         SELECT id, cpf, senha, NOW() FROM motoristas"
    );
    $backupCount = (int) $pdo->query(
        "SELECT COUNT(*) FROM motoristas_senha_backup
         WHERE backup_at = (SELECT MAX(backup_at) FROM motoristas_senha_backup)"
    )->fetchColumn();

    // 4) UPDATE em massa
    $st = $pdo->prepare("UPDATE motoristas SET senha = :senha");
    $st->execute([':senha' => $HASH]);
    $afetados = $st->rowCount();

    echo json_encode([
        'status' => 'ok',
        'mensagem' => 'Senhas redefinidas com sucesso.',
        'motoristas_atualizados' => $afetados,
        'backup_registros' => $backupCount,
        'nova_senha' => $NOVA_SENHA,
        'aviso' => 'APAGUE este arquivo agora. Backup salvo em motoristas_senha_backup.',
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
