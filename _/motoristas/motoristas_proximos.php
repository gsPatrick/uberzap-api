<?php
/**
 * Motoristas online por perto (radar de carrinhos no mapa).
 * Usado pelo app do passageiro e do motorista.
 * POST: secret, cidade_id, [excluir_id]
 * Retorna: [{ id, latitude, longitude, veiculo, online, categorias }]
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/motoristas.php';

try {
    $secret = $_POST['secret'] ?? '';
    $s = new seguranca();
    if (!$s->compare_secret($secret)) {
        http_response_code(401);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cidade_id = (int) ($_POST['cidade_id'] ?? 0);
    $excluir = (int) ($_POST['excluir_id'] ?? 0);
    if ($cidade_id < 1) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $m = new Motoristas();
    $lista = $m->get_motoristas_proximos($cidade_id);

    $out = [];
    foreach ($lista as $mot) {
        if ($excluir && (int) $mot['id'] === $excluir) {
            continue;
        }
        $out[] = [
            'id' => (int) $mot['id'],
            'latitude' => $mot['latitude'],
            'longitude' => $mot['longitude'],
            'veiculo' => $mot['veiculo'] ?? '',
            'online' => (int) ($mot['online'] ?? 0),
            'categorias' => $mot['ids_categorias'] ?? '[]',
        ];
    }

    echo json_encode($out, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[motoristas_proximos.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao buscar motoristas'], JSON_UNESCAPED_UNICODE);
}
