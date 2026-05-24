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
require_once __DIR__ . '/../classes/expo_push.php';
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
    $avaliacao_pendente = null;

    if ($historico && count($historico) > 0) {
        $ultima = $historico[0];
        $ultimo_status = (int) $ultima['status'];

        if ($ultimo_status === 4 && !$a->verifica_avaliacao($ultima['id'])) {
            $avaliacao_pendente = [
                'id' => $ultima['id'],
                'taxa' => $ultima['taxa'],
                'lat_ini' => $ultima['lat_ini'],
                'lng_ini' => $ultima['lng_ini'],
                'lat_fim' => $ultima['lat_fim'],
                'lng_fim' => $ultima['lng_fim'],
                'endereco_ini_txt' => $ultima['endereco_ini_txt'] ?? '',
                'endereco_fim_txt' => $ultima['endereco_fim_txt'] ?? '',
            ];

            if (!empty($ultima['motorista_id'])) {
                $dados_motorista = $m->get_motorista($ultima['motorista_id']);
                if ($dados_motorista) {
                    $avaliacao_pendente['motorista'] = [
                        'id' => $dados_motorista['id'],
                        'nome' => $dados_motorista['nome'],
                        'foto' => $dados_motorista['img'],
                        'placa' => $dados_motorista['placa'],
                        'veiculo' => $dados_motorista['veiculo'],
                        'rating' => $a->get_media_avaliacoes($dados_motorista['id']),
                    ];
                }
            }
        }
    }

    echo json_encode([
        'status' => 0,
        'avaliacao_pendente' => $avaliacao_pendente,
    ], JSON_UNESCAPED_UNICODE);
    exit;
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
        ExpoPush::notifyPassengerByClienteId($cliente_id, 5, 'Motorista', $corrida['id']);
        ExpoPush::notifyOnlineDriversRideUnavailable(
            $corrida['cidade_id'],
            $corrida['categoria_id'] ?? 0,
            $corrida['id'],
            null,
            'A corrida expirou e foi cancelada.'
        );
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
        $latMot = $dados_motorista['latitude'] ?? null;
        $lngMot = $dados_motorista['longitude'] ?? null;
        $latIni = $corrida['lat_ini'] ?? null;
        $lngIni = $corrida['lng_ini'] ?? null;
        if ($latMot && $lngMot && $latIni && $lngIni) {
            $dados_tempo = $mapbox->getDistanciaETempo($latMot, $lngMot, $latIni, $lngIni);
            if ($dados_tempo) {
                $tempo = (int) round($dados_tempo['tempo'] / 60);
            }
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
