<?php
include("../classes/motoristas.php");
include("../classes/corridas.php");
include("../classes/localizacao_corridas.php");


$m = new Motoristas();
$c = new Corridas();
$lc = new localizacao_corridas();

$id_motorista = $_POST['id_motorista'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$m->atualiza_coordenadas($id_motorista, $latitude, $longitude);

$corridas = $c->get_corridas_abertas($id_motorista);
if($corridas){ 
    $corrida = $corridas[0]; 
    //se status == 2 ou 3, atualiza a localização
    if($corrida['status'] == 2 || $corrida['status'] == 3){
        $corrida_id = $corrida['id'];
        $lc->insereLocation($corrida_id, $latitude, $longitude);
    }
    echo json_encode("ok");
}else{
    echo "no";
}
?>