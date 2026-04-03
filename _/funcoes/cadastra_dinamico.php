<?php  
header("access-control-allow-origin: *");
include_once "../classes/dinamico_mapa.php";
$dm = new dinamico_mapa();
$cidade_id = $_POST['cidade_id'];
$nome = $_POST['nome'];
$raio = $_POST['raio'];
$adicional = $_POST['adicional'];
$latitude = $_POST['lat'];
$longitude = $_POST['lng'];

$dados = $dm->cadastra($cidade_id, $nome, $latitude, $longitude, $raio, $adicional);
if ($dados) {
    echo "ok";
} else {
    echo "erro";
}
?>
