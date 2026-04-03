<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include ("../classes/corridas.php");
include ("../classes/tempo.php");
include ("../classes/motoristas.php");
include ("../classes/avaliacoes.php");

$c = new Clientes();
$m = new corridas();
$t = new tempo();
$mt = new motoristas();
$a = new avaliacoes();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cliente = $c ->login($telefone, $senha);
if($cliente){
    $cliente_id = $cliente['id'];
    $corridas = $m ->get_all_corridas_cliente($cliente_id);
    foreach($corridas as $key => $corrida){
        $corridas[$key]['date'] = $t ->data_mysql_para_user($corrida['date']) . " às " . $t ->hora_mysql_para_user($corrida['date']);
        $corridas[$key]['motorista'] = $mt ->get_motorista($corrida['motorista_id'])['nome'];
        $corridas[$key]['status'] = $m ->status_string($corrida['status']);
        if($a ->get_avaliacao($corrida['id'])['nota']){
        $corridas[$key]['avaliacao'] = $a ->get_avaliacao($corrida['id'])['nota'];
        }else{
            $corridas[$key]['avaliacao'] = 0;
        }
    }
    if(count($corridas) > 0){
        echo json_encode($corridas);
    }else{
        echo "";
    }
}

?>