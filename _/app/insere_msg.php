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
    $mensagem = $_POST['msg'];
    $cliente_id = $cliente['id'];
    $corridas = $crr->get_all_corridas_cliente($cliente_id);
    if($corridas){
        $corrida = $corridas[0];
        $corrida_id = $corrida['id'];
        $m -> insere_msg($corrida_id, $mensagem, 2);
        echo "ok";
    }else{
        echo "no";
    }
}else{
    echo "invalid request";
}