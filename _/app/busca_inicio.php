<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/clientes.php";

$crr = new corridas();
$c = new Clientes();


$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];
    $resposta = array();
    $corridas = $crr->get_all_corridas_cliente($cliente_id);
    if ($corridas) {
        $aberta = false;
        foreach($corridas as $corrida){
            if($corrida['status'] == 0 || $corrida['status'] == 1 || $corrida['status'] == 2 || $corrida['status'] == 3){
                $aberta = true;
                
            }
        }
        if($aberta){
            echo "1";
        }else{
            echo "";
        }
    } else {
        echo "";
    }
}
