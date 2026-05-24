<?php
/**
 * Health check — testa conexão PDO (útil no Easypanel).
 * GET /_/bd/health_db.php
 */
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

try {
    require_once __DIR__ . '/conexao.php';
    global $pdo;
    $pdo->query('SELECT 1');
    echo json_encode([
        'status' => 'ok',
        'mensagem' => 'Conexão com banco OK',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Falha na conexão com banco',
        'detalhe' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
