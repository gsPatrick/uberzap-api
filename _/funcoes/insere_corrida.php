<?php 
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/corridas.php");
include("../classes/status_historico.php");

$corridas = new corridas();
$status_historico = new status_historico();
$cidade_id = $_POST['cidade_id'];
$endereco_ini = $_POST['endereco_ini'];
$endereco_fim = $_POST['endereco_fim'];
$categoria_id = $_POST['categoria_id'];
$lat_ini = $_POST['lat_ini'];
$lng_ini = $_POST['lng_ini'];
$lat_fim = $_POST['lat_fim'];
$lng_fim = $_POST['lng_fim'];
$nome = $_POST['nome'];
$km = $_POST['km'];
$tempo = $_POST['tempo'];
$taxa = $_POST['taxa'];

$id = $corridas->insere_corrida(0, 0, $cidade_id, $lat_ini, $lng_ini, $lat_fim, $lng_fim, $km, $tempo, $endereco_ini, $endereco_fim, $taxa, "Combinar com Cliente", 0, 0, 0, $categoria_id, $nome);
//alterar o status mais tarde

$status_historico->salva_status($id, "Corrida solicitada", "Painel de Controle");
echo json_encode(array("id" => $id, "status" => "ok"));

?>
