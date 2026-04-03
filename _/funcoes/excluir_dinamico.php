<?php 
header("access-control-allow-origin: *");
include_once "../classes/dinamico_mapa.php";
$dm = new dinamico_mapa();
$id = $_POST['id'];

$excluir = $dm->delete_dinamico_mapa($id);
if ($excluir) {
    echo "ok";
} else {
    echo "erro";
}
?>