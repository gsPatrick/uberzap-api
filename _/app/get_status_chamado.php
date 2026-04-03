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
            $resposta['motorista'] = $dados_motorista['nome'];
            $resposta['motorista_id'] = $dados_motorista['id']; 
            $resposta['motorista_img'] = $dados_motorista['img'];
            $resposta['latitude'] = $dados_motorista['latitude'];
            $resposta['longitude'] = $dados_motorista['longitude'];
            $resposta['placa'] = $dados_motorista['placa'];
            $resposta['veiculo'] = $dados_motorista['veiculo'];
            $resposta['motorista_nome'] = $dados_motorista['nome'];
            $resposta['avaliacao'] = $a->get_media_avaliacoes($dados_motorista['id']);
            $resposta['lat_ini'] = $corrida['lat_ini'];
            $resposta['lng_ini'] = $corrida['lng_ini']; 
            $resposta['lat_fim'] = $corrida['lat_fim'];
            $resposta['lng_fim'] = $corrida['lng_fim'];  

            //se status for 1 calcula o tempo entre o motorista e o cliente
            if ($status == 1) {
                $maps = new Maps();
                //$tempo = $maps->calcularTempo($dados_motorista['latitude'], $dados_motorista['longitude'], $corrida['lat_ini'], $corrida['lng_ini']);
                $dados_tempo = $mapbox->getDistanciaETempo($dados_motorista['latitude'], $dados_motorista['longitude'], $corrida['lat_ini'], $corrida['lng_ini']);
                if($dados_tempo){
                    $tempo = (int) round($dados_tempo['tempo'] / 60); //converte para minutos (inteiro, sem casas decimais)
                } else {
                    $tempo = 0;
                }
                $resposta['tempo_motorista'] = $tempo;
            } else {
                $resposta['tempo_motorista'] = 0;
            }

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
