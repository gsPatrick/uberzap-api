<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../bd/config.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/clientes.php';
require_once __DIR__ . '/../classes/motoristas.php';
require_once __DIR__ . '/../classes/mensagens.php';
require_once __DIR__ . '/../classes/avaliacoes.php';
require_once __DIR__ . '/../classes/tempo.php';
require_once __DIR__ . '/../classes/status_historico.php';
require_once __DIR__ . '/../classes/maps.php';
require_once __DIR__ . '/../classes/mapbox.php';

$crr = new corridas();
$c = new Clientes();
$m = new Motoristas();
$msg = new mensagens();
$a = new avaliacoes();
$tmp = new tempo();
$sh = new status_historico();
$mapbox = new Mapbox(MAPBOX_KEY);

$senha = $_POST['senha'] ?? '';
$telefone = $_POST['telefone'] ?? '';

$cliente = $c->login($telefone, $senha);

if (!$cliente) {
    echo json_encode(['status' => 0], JSON_UNESCAPED_UNICODE);
    exit;
}

$cliente_id = $cliente['id'];
$corridas_abertas = $crr->getAllCorridasAbertasCliente($cliente_id, $telefone);

if ($corridas_abertas && count($corridas_abertas) > 0) {
    $corrida = $corridas_abertas[0];
} else {
    $historico = $crr->get_all_corridas_cliente($cliente_id, $telefone);
    if (!$historico || count($historico) === 0) {
        echo json_encode(['status' => 0], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ultima = $historico[0];
    $ultimo_status = (int) $ultima['status'];

    // Corrida encerrada/cancelada: só reabre fluxo de avaliação se finalizada e ainda não avaliada
    if ($ultimo_status === 4 && !$a->verifica_avaliacao($ultima['id'])) {
        $corrida = $ultima;
    } else {
        echo json_encode(['status' => 0], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$status = (int) $corrida['status'];

if ($status === 0) {
    $tempo_cancelamento = 5;
    $tmp->data_fim = $corrida['date'];
    $tmp->data_ini = date('Y-m-d H:i:s');
    $tempo = $tmp->tempo_passou('minutos');
    if ($tempo > $tempo_cancelamento) {
        $crr->set_status($corrida['id'], 5);
        $sh->salva_status($corrida['id'], 'Cancelado automaticamente após ' . $tempo_cancelamento . ' minutos', 'Sistema');
        echo json_encode(['status' => 0], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$resposta = [];
$status_string = $crr->status_string($status);

if (in_array($status, [1, 2, 3, 4], true) && !empty($corrida['motorista_id'])) {
    $dados_motorista = $m->get_motorista($corrida['motorista_id']);

    $tempo = 0;
    if ($status === 1 && $dados_motorista) {
        $dados_tempo = $mapbox->getDistanciaETempo(
            $dados_motorista['latitude'],
            $dados_motorista['longitude'],
            $corrida['lat_ini'],
            $corrida['lng_ini']
        );
        if ($dados_tempo) {
            $tempo = (int) round($dados_tempo['tempo'] / 60);
        }
    }

    if ($dados_motorista) {
        $resposta['motorista'] = [
            'id' => $dados_motorista['id'],
            'nome' => $dados_motorista['nome'],
            'foto' => $dados_motorista['img'],
            'latitude' => $dados_motorista['latitude'],
            'longitude' => $dados_motorista['longitude'],
            'placa' => $dados_motorista['placa'],
            'veiculo' => $dados_motorista['veiculo'],
            'rating' => $a->get_media_avaliacoes($dados_motorista['id']),
            'tempo_chegada' => ($status === 1 ? $tempo . ' min' : '0 min'),
        ];
    }
}

$resposta['status'] = $status;
$resposta['status_string'] = $status_string;
$resposta['id'] = $corrida['id'];
$resposta['taxa'] = $corrida['taxa'];
$resposta['lat_ini'] = $corrida['lat_ini'];
$resposta['lng_ini'] = $corrida['lng_ini'];
$resposta['lat_fim'] = $corrida['lat_fim'];
$resposta['lng_fim'] = $corrida['lng_fim'];
$resposta['pendente_avaliacao'] = ($status === 4 && !$a->verifica_avaliacao($corrida['id']));

$mensagens = $msg->get_all_msg($corrida['id']);
$resposta['msg'] = $mensagens ?: '';

echo json_encode($resposta, JSON_UNESCAPED_UNICODE);
