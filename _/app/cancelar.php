<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/clientes.php";
include_once "../classes/alertas_motoristas.php";
include_once "../classes/status_historico.php";

$crr = new corridas();
$c = new Clientes();
$am = new alertas_motoristas();
$sh = new status_historico();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];
    $valor_multa = 5.00; // Valor fixo da multa
    $tempo_tolerancia = 300; // 5 minutos em segundos

    // Verifica se tem corrida em andamento
    $corridas_em_andamento = $crr->getAllCorridasAbertasCliente($cliente_id);
    if($corridas_em_andamento){
        $corrida = $corridas_em_andamento[0];
        $id_corrida = $corrida['id'];
        $motorista_id = $corrida['motorista_id'];
        
        // Só aplica multa se já tiver motorista aceito
        if($motorista_id != 0){
            $msg = "Corrida cancelada pelo cliente"; 
            $am->insere($motorista_id, $msg);
            
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
                    // Aplica multa no banco de dados da corrida
                    $crr->aplicaTaxaCancelamento($id_corrida, $valor_multa);
                    // Deduz do saldo do cliente
                    $c->deduz_saldo($cliente_id, $valor_multa);
                }
            }
        }
    }

    if($crr->cancelar_corrida($cliente_id)){ // Cancela a corrida (status 5)
        echo "ok";
    }else{
        echo "erro";
    }
}
