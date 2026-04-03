<?php 
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/status_historico.php");
include_once "../classes/clientes.php";
include_once "../classes/transacoes.php";
include '../classes/cupons.php';


$corridas = new corridas();
$status_historico = new status_historico();
$c = new Clientes();
$cupons = new cupons();


$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$valor = $_POST['valor'];

$cliente = $c ->login($telefone, $senha);
$resposta = array();
if($cliente){
$cidade_id = $cliente['cidade_id'];
$nome = $cliente['nome'];
$cliente_id = $cliente['id'];
$transacoes = new transacoes($cidade_id);
$forma_pagamento = $_POST['forma_pagamento'];
$endereco_ini = $_POST['endereco_ini'];
$endereco_fim = $_POST['endereco_fim'];
$categoria_id = $_POST['categoria_id'];
$lat_ini = $_POST['lat_ini'];
$lng_ini = $_POST['lng_ini'];
$lat_fim = $_POST['lat_fim'];
$lng_fim = $_POST['lng_fim'];
$obs = $_POST['obs'] ?? "";
$cupom = $_POST['cupom'] ?? "";
if($cupom != ""){
    $obs .= "\nCupom utilizado: " . $cupom . "\n";
}

$km = $_POST['km'];
$tempo = $_POST['tempo'];
$taxa = $_POST['taxa'];

$id = $corridas->insere_corrida(0, $cliente_id, $cidade_id, $lat_ini, $lng_ini, $lat_fim, $lng_fim, $km, $tempo, $endereco_ini, $endereco_fim, $taxa, $forma_pagamento, 0, 0, 0, $categoria_id, $nome);
//alterar o status mais tarde
if($obs != ""){
    $corridas->setObs($id, $obs);
}
if($cupom != ""){
    $dados_cupom = $cupons->get_cupon_nome($cupom, $cidade_id);
    if($dados_cupom){
        $cupons->add_cupon_used($cidade_id, $cupom, $cliente_id);
        $cupons->diminui_quantidade($dados_cupom['id']);
    }
}

$status_historico->salva_status($id, "Corrida solicitada", "Aplicativo");
echo json_encode(array("id" => $id, "status" => "ok"));
}
