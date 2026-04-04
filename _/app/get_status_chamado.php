<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/clientes.php";
include_once "../classes/motoristas.php";
include_once "../classes/mensagens.php";
include_once "../classes/avaliacoes.php";
include_once "../classes/tempo.php";
include_once "../classes/status_historico.php"; 
include_once "../classes/maps.php";
include_once "../classes/mapbox.php";

$crr = new corridas();
$c = new Clientes();
$m = new Motoristas();
$msg = new mensagens();
$a = new avaliacoes();
$tmp = new tempo();
$sh = new status_historico();
$mapbox = new Mapbox(MAPBOX_KEY);

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];
    $resposta = array();
    $corridas = $crr->get_all_corridas_cliente($cliente_id);
    //pega a última corrida
    if ($corridas) {
        $corrida = $corridas[0];
        $status = $corrida['status'];

        if ($status == 0) {
            $tempo_cancelamento = 5; //tempo em minutos
            if ($tempo_cancelamento) {
                $tmp->data_fim = $corrida['date'];
                $tmp->data_ini = date('Y-m-d H:i:s');
                $tempo = $tmp->tempo_passou("minutos");
                if ($tempo > $tempo_cancelamento) {
                    $crr->set_status($corrida['id'], 5);
                    $sh->salva_status($corrida['id'], "Cancelado automaticamente após " . $tempo_cancelamento . " minutos", "Sistema");
                    $corrida = $crr->get_corrida_id($corrida['id']);
                    $status = $corrida['status'];
                }
            }
        }

        $status_string = $crr->status_string($status);
        if ($status == 1 || $status == 2 || $status == 3 || $status == 4) { 
            $dados_motorista = $m->get_motorista($corrida['motorista_id']);
            
            // Calcula o tempo do motorista se status for 1
            $tempo = 0;
            if ($status == 1) {
                $dados_tempo = $mapbox->getDistanciaETempo($dados_motorista['latitude'], $dados_motorista['longitude'], $corrida['lat_ini'], $corrida['lng_ini']);
                if($dados_tempo){
                    $tempo = (int) round($dados_tempo['tempo'] / 60);
                }
            }

            $resposta['motorista'] = array(
                'id' => $dados_motorista['id'],
                'nome' => $dados_motorista['nome'],
                'foto' => $dados_motorista['img'],
                'latitude' => $dados_motorista['latitude'],
                'longitude' => $dados_motorista['longitude'],
                'placa' => $dados_motorista['placa'],
                'veiculo' => $dados_motorista['veiculo'],
                'rating' => $a->get_media_avaliacoes($dados_motorista['id']),
                'tempo_chegada' => ($status == 1 ? $tempo . " min" : "0 min")
            );

            $resposta['lat_ini'] = $corrida['lat_ini'];
            $resposta['lng_ini'] = $corrida['lng_ini']; 
            $resposta['lat_fim'] = $corrida['lat_fim'];
            $resposta['lng_fim'] = $corrida['lng_fim'];  
        }
        $resposta['status'] = $status;
        $resposta['status_string'] = $status_string;
        $resposta['id'] = $corrida['id'];
        $resposta['taxa'] = $corrida['taxa'];

        $mensagens = $msg->get_all_msg($corrida['id']);
        if ($mensagens) {
            $resposta['msg'] = $mensagens;
        } else {
            $resposta['msg'] = "";
        }

        echo json_encode($resposta);
    } else {
        echo json_encode(array("status" => 0));
    }
}
