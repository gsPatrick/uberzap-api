<?php
include("../classes/motoristas.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/clientes.php");
include("../classes/tempo.php");
include("../classes/categorias.php");

$s = new seguranca();
$c = new corridas();
$t = new tempo();
$clientes = new clientes();
$cat = new categorias();
$m = new motoristas();
$id_motorista = $_GET['id_motorista'];
$dados_motorista = $m->get_motorista($id_motorista);
if ($dados_motorista['online'] != 1) {
    echo "no";
    exit;
}
$ultimo_id = 0;
$cidade_id = $dados_motorista['cidade_id'];
$corridas  = $c->get_corridas_disponiveis($cidade_id);
$ids_categorias = $dados_motorista['ids_categorias'];
$ids_categorias = json_decode($ids_categorias);
if ($corridas) {
    foreach ($corridas as $key => $value) {
        $id_categoria = $value['categoria_id'];
        if (!in_array($id_categoria, $ids_categorias)) {
            unset($corridas[$key]);
        }
        if($value['id'] > $ultimo_id){
            $ultimo_id = $value['id'];
        }
    }
    //reorganiza indices
    $corridas = array_values($corridas);
    if (count($corridas) == 0) {
        echo "no";
        exit;
    } else {
        echo $ultimo_id;
    }

    //var_dump($ids_categorias);
} else {
    echo "no";
}
