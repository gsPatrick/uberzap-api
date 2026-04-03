<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include ("../classes/mensagens.php");
include_once "../classes/tempo.php";
include_once "../classes/corridas.php";

$c = new Clientes();
$m = new mensagens();
$t = new tempo();
$crr = new corridas();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cliente = $c ->login($telefone, $senha);
if($cliente){
    $cliente_id = $cliente['id'];
    $corridas = $crr->get_all_corridas_cliente($cliente_id);
    if($corridas){
        $corrida = $corridas[0];
        $mensagens = $m ->get_all_msg($corrida['id']);
        if($mensagens){
            foreach ($mensagens as $key => $value) {
                $mensagens[$key]['hora'] = $t->data_mysql_para_user($value['date']) . " às " . $t->hora_mysql_para_user($value['date']);
            }
            echo json_encode($mensagens);
        }else{
            echo "no";
        }
    }else{
        echo "no";
    }
}else{
    echo "invalid request";
}