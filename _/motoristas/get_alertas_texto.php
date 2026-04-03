<?php
include ("../classes/corridas.php");
include_once "../classes/alertas_motoristas.php";
$am = new alertas_motoristas();

$id_motorista = $_POST['id_motorista'];

//busca alertas do motorista
$alertas = $am->getByMotorista($id_motorista);

//set como executado
foreach ($alertas as $alerta) {
    $am->setExecutado($alerta['id']);
}

if($alertas){
    //pega ultimo alerta
    $ultimo_alerta = $alertas[0];
    echo $ultimo_alerta['msg'];
}else{
    echo "no";
}

?>