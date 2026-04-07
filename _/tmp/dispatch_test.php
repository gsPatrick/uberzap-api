<?php
include_once "../classes/corridas.php";
$c = new corridas();

// Pega o valor da URL ou usa 90.00 como padrão
$taxa = isset($_GET['valor']) ? $_GET['valor'] : "90.00";
$endereco_ini = isset($_GET['ini']) ? $_GET['ini'] : "Av. Paulista, 1000 - Bela Vista, São Paulo, SP";
$endereco_fim = isset($_GET['fim']) ? $_GET['fim'] : "Rua Oscar Freire, 500 - Jardins, São Paulo, SP";

$id = $c->insere_corrida(
    0, 1, 1, 
    -23.561706, -46.655981, 
    -23.567087, -46.667232, 
    "2.5", "10", 
    $endereco_ini, 
    $endereco_fim, 
    $taxa, 
    "Dinheiro", "Pendente", "DYNAMO_".time(), "", 1, "Teste Dinâmico"
);

if($id) echo "CORRIDA DE R$ $taxa DISPARADA! ID: " . $id;
else echo "ERRO AO DISPARAR.";
?>
