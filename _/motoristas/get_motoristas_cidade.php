<?php
header('access-control-allow-origin: *');
include("../classes/motoristas.php");
$m = new motoristas();

$cidade_id = $_POST['cidade_id'];
$motoristas = $m->get_all_motoristas($cidade_id);
$dados_retorno = array();
if ($motoristas) {
    foreach ($motoristas as $motorista) {
        $dados_retorno[] = array(
            "id" => $motorista['id'],
            "latitude" => $motorista['latitude'],
            "longitude" => $motorista['longitude'],
            "ativo" => $motorista['ativo'],
            "online" => $motorista['online']
        );
    }
    if (count($dados_retorno) > 0) {
        echo json_encode($dados_retorno);
    } else {
        echo "";
    }
} else {
    echo "";
}
