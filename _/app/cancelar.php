<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/clientes.php";
include_once "../classes/alertas_motoristas.php";
include_once "../classes/status_historico.php";
include_once "../classes/expo_push.php";
include_once "../classes/motoristas.php";

$crr = new corridas();
$c = new Clientes();
$am = new alertas_motoristas();
$sh = new status_historico();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];
    $valor_multa = 5.00;
    $tempo_tolerancia = 300;
    $rideId = null;
    $motorista_id = 0;
    $cidade_id = null;
    $categoria_id = 0;

    $corridas_em_andamento = $crr->getAllCorridasAbertasCliente($cliente_id);
    if ($corridas_em_andamento) {
        $corrida = $corridas_em_andamento[0];
        $id_corrida = $corrida['id'];
        $rideId = $id_corrida;
        $motorista_id = $corrida['motorista_id'];
        $cidade_id = $corrida['cidade_id'] ?? $cliente['cidade_id'];
        $categoria_id = $corrida['categoria_id'] ?? 0;
        
        // Só aplica multa se já tiver motorista aceito
        if($motorista_id != 0){
            $msg = "Corrida cancelada pelo cliente"; 
            $am->insere($motorista_id, $msg);
            ExpoPush::notifyDriverPassengerCancelledById($motorista_id, $rideId);
            
            // Busca histórico para ver hora do aceite
            $historico = $sh->get_status($id_corrida);
            $hora_aceite = null;
            
            foreach($historico as $item) {
                if(strpos($item['status'], 'aceitou a corrida') !== false) {
                    $hora_aceite = $item['hora'];
                    break;
                }
            }
            
            if($hora_aceite) {
                $segundos_passados = time() - strtotime($hora_aceite);
                
                if($segundos_passados > $tempo_tolerancia) {
                    // Deduz do saldo do cliente; taxa fica em cancelar_corrida (status 5)
                    $c->deduz_saldo($cliente_id, $valor_multa);
                    $crr->atualiza_taxa($id_corrida, $valor_multa);
                }
            }
        }
    }

    if($crr->cancelar_corrida($cliente_id)){
        ExpoPush::notifyPassengerTripStatus($cliente, 5, 'Motorista', $rideId);
        if ($motorista_id == 0 && $cidade_id && $rideId) {
            ExpoPush::notifyOnlineDriversRideUnavailable(
                $cidade_id,
                $categoria_id,
                $rideId,
                null,
                'O passageiro cancelou a corrida.'
            );
        }
        echo "ok";
    }else{
        echo "erro";
    }
}
