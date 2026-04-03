<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include_once "../classes/clientes.php";
include_once "../classes/alertas_motoristas.php";

$crr = new corridas();
$c = new Clientes();
$am = new alertas_motoristas();


$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];

    //verifica se tem corrida em andamento
    $corridas_em_andamento = $crr->getAllCorridasAbertasCliente($cliente_id);
    if($corridas_em_andamento){
        //pega a ultima corrida
        $corrida = $corridas_em_andamento[0];
        $motorista_id = $corrida['motorista_id'];
        if($motorista_id != 0){
            $msg = "Corrida cancelada pelo cliente"; //insere alerta para o motorista
            $am->insere($motorista_id, $msg);
        }
    }


    if($crr->cancelar_corrida($cliente_id)){ //cancela a corrida
        echo "ok";
    }else{
        echo "erro";
    }
}
