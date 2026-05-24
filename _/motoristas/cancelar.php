<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/status_historico.php';
require_once __DIR__ . '/../classes/motoristas.php';
require_once __DIR__ . '/../classes/clientes.php';
require_once __DIR__ . '/../classes/expo_push.php';

$secret_key = $_POST['secret'] ?? '';

$s = new seguranca();
if (!$s->compare_secret($secret_key)) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$id_corrida = $_POST['id_corrida'] ?? '';
if ($id_corrida === '') {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'id_corrida obrigatório'], JSON_UNESCAPED_UNICODE);
    exit;
}

$c = new corridas();
$sh = new status_historico();
$m = new Motoristas();

$corrida = $c->get_corrida_id($id_corrida);
if (!$corrida) {
    http_response_code(404);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Corrida não encontrada'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nome_motorista = 'Motorista';
if (!empty($corrida['motorista_id'])) {
    $dados_motorista = $m->get_motorista($corrida['motorista_id']);
    if ($dados_motorista && !empty($dados_motorista['nome'])) {
        $nome_motorista = $dados_motorista['nome'];
    }
}

// Status 5 = cancelada — libera o passageiro imediatamente
$c->set_status($id_corrida, 5);
$c->altera_motorista($id_corrida, 0);
$sh->salva_status($id_corrida, $nome_motorista . ' cancelou a corrida', 'App Motorista');

$cl = new Clientes();
$dados_cliente = $cl->get_cliente_id($corrida['cliente_id']);
if ($dados_cliente) {
    ExpoPush::notifyPassengerTripStatus($dados_cliente, 5, $nome_motorista, $id_corrida);
}

echo 'ok';
