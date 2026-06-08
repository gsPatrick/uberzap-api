<?php
/**
 * Avaliações de um motorista (para o passageiro ver o perfil).
 * POST: secret, id_motorista
 * Retorna: { media, total, avaliacoes: [{ nota, comentario, date, nome_cliente }] }
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';

try {
    $secret = $_POST['secret'] ?? '';
    $s = new seguranca();
    if (!$s->compare_secret($secret)) {
        http_response_code(401);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = (int) ($_POST['id_motorista'] ?? 0);
    if ($id < 1) {
        echo json_encode(['media' => 0, 'total' => 0, 'avaliacoes' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }

    global $pdo;

    // Média e total (só notas válidas)
    $st = $pdo->prepare("SELECT COUNT(*) AS total, AVG(nota) AS media FROM avaliacoes WHERE motorista_id = :id AND nota > 0");
    $st->execute([':id' => $id]);
    $agg = $st->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'media' => 0];

    // Lista de avaliações com o nome do passageiro
    $st2 = $pdo->prepare(
        "SELECT a.nota, a.comentario, a.date, c.nome AS nome_cliente
         FROM avaliacoes a
         LEFT JOIN clientes c ON c.id = a.cliente_id
         WHERE a.motorista_id = :id
         ORDER BY a.id DESC
         LIMIT 30"
    );
    $st2->execute([':id' => $id]);
    $rows = $st2->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'nota' => (int) ($r['nota'] ?? 0),
            'comentario' => $r['comentario'] ?? '',
            'date' => $r['date'] ?? '',
            'nome_cliente' => trim((string) ($r['nome_cliente'] ?? '')) ?: 'Passageiro',
        ];
    }

    echo json_encode([
        'media' => round((float) ($agg['media'] ?? 0), 1),
        'total' => (int) ($agg['total'] ?? 0),
        'avaliacoes' => $out,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[get_avaliacoes.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao buscar avaliações'], JSON_UNESCAPED_UNICODE);
}
