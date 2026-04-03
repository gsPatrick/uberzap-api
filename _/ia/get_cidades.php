<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/cidades.php");

$c = new cidades();

$dados = $c->get_cidades();

$retorno = array();

foreach ($dados as $dado) {
    $retorno["cidades"][] = array(
        'id' => $dado['id'],
        'nome' => $dado['nome'],
    );
}

header('Content-Type: application/json');
echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
?>

