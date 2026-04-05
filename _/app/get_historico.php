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

$senha = isset($_POST['senha']) ? $_POST['senha'] : $_GET['senha'];
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : $_GET['telefone'];

$cliente = $c ->login($telefone, $senha);
if($cliente){
    $cliente_id = $cliente['id'];
    $corridas = $m ->get_all_corridas_cliente($cliente_id);
    if($corridas && is_array($corridas)){
        foreach($corridas as $key => $corrida){
            $corridas[$key]['date'] = $t ->data_mysql_para_user($corrida['date']) . " às " . $t ->hora_mysql_para_user($corrida['date']);
            $motorista_data = $mt ->get_motorista($corrida['motorista_id']);
            $corridas[$key]['motorista'] = $motorista_data['nome'];
            $corridas[$key]['veiculo'] = $motorista_data['veiculo'];
            $corridas[$key]['placa'] = $motorista_data['placa'];
            $corridas[$key]['foto_motorista'] = $motorista_data['foto'];
            $corridas[$key]['status'] = $m ->status_string($corrida['status']);
            $corridas[$key]['endereco_ini'] = $corrida['endereco_ini_txt'];
            $corridas[$key]['endereco_fim'] = $corrida['endereco_fim_txt'];
            $corridas[$key]['valor'] = $corrida['taxa'];
            
            if($a ->get_avaliacao($corrida['id'])['nota']){
                $corridas[$key]['avaliacao'] = $a ->get_avaliacao($corrida['id'])['nota'];
            }else{
                $corridas[$key]['avaliacao'] = 0;
            }
        }
        echo json_encode($corridas);
    }else{
        echo json_encode(array());
    }
}else{
    echo json_encode(array("status" => "erro", "mensagem" => "Login falhou"));
}

?>