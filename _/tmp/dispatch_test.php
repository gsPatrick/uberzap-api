<?php
include_once "../classes/corridas.php";

$c = new corridas();

// Parâmetros da Corrida de Teste
$motorista_id = 0; // Procurando
$cliente_id = 1; 
$cidade_id = 1;
$lat_ini = -23.561706;
$lng_ini = -46.655981;
$lat_fim = -23.567087;
$lng_fim = -46.667232;
$km = "2.5";
$tempo = "10";
$endereco_ini_txt = "Av. Paulista, 1000 - Bela Vista, São Paulo, SP";
$endereco_fim_txt = "Rua Oscar Freire, 500 - Jardins, São Paulo, SP";
$taxa = "90.00";
$f_pagamento = "Dinheiro";
$status_pagamento = "Pendente";
$ref_pagamento = "TEST_90";
$cupom = "";
$categoria_id = 1;
$nome_cliente = "Cliente de Teste 90";

$id = $c->insere_corrida(
    $motorista_id, 
    $cliente_id, 
    $cidade_id, 
    $lat_ini, 
    $lng_ini, 
    $lat_fim, 
    $lng_fim, 
    $km, 
    $tempo, 
    $endereco_ini_txt, 
    $endereco_fim_txt, 
    $taxa, 
    $f_pagamento, 
    $status_pagamento, 
    $ref_pagamento, 
    $cupom, 
    $categoria_id, 
    $nome_cliente
);

if($id) {
    echo "CORRIDA DE R$ 90,00 DISPARADA COM SUCESSO! ID: " . $id;
} else {
    echo "ERRO AO DISPARAR CORRIDA.";
}
?>
