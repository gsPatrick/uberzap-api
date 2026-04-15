<?php
include_once "../classes/motoristas.php";
$m = new motoristas();
$id = 5; // ID que você me mostrou no debug anterior
$d = $m->get_motorista($id);

if ($d) {
    echo "MOTORISTA ID: " . $d['id'] . "\n";
    echo "CIDADE ID: " . $d['cidade_id'] . "\n";
    echo "CATEGORIAS (JSON): " . $d['ids_categorias'] . "\n";
    echo "ONLINE: " . $d['online'] . "\n";
} else {
    echo "MOTORISTA NÃO ENCONTRADO.";
}
?>
