<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/categorias.php");
include("../classes/cidades.php");
$c = new categorias();
$cidades = new cidades();

$cidade_index = $_POST['cidade_index'];
$cidade = $cidades->getByIndex($cidade_index);
$cidade_id = $cidade['id'];

$dados_retorno = array();
$dados = $c->get_categorias($cidade_id, true);

$retorno = array();

foreach ($dados as $dado) {
    $retorno["categorias"][] = array(
        'id' => $dado['id'],
        'nome' => $dado['nome'],
    );
}

header('Content-Type: application/json');
echo json_encode($retorno, JSON_UNESCAPED_UNICODE);


