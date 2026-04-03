<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include ("../classes/motoristas.php");

$c = new Clientes();
$m = new motoristas();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cliente = $c ->login($telefone, $senha);
if($cliente){
    $cidade_id = $cliente['cidade_id'];
    $motoristas = $m ->get_all_motoristas($cidade_id);
    $dados_retorno = array();
    if($motoristas){
        foreach($motoristas as $motorista){
            $dados_retorno[] = array(
                "id" => $motorista['id'],
                "latitude" => $motorista['latitude'],
                "longitude" => $motorista['longitude'],
                "ativo" => $motorista['ativo'],
                "online" => $motorista['online']
            );
        }
        if(count($dados_retorno) > 0){
            echo json_encode($dados_retorno);
        }else{
            echo "";
        }
    }else{
        echo "";
    }
}else{
    echo "";
}