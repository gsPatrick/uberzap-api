<?php 
include "../classes/corridas.php";
include '../classes/status_historico.php';
include '../classes/motoristas.php';
$sh = new status_historico();
$c = new corridas();
$m = new Motoristas();

if(isset($_POST['motorista'])) {
    $motorista = $_POST['motorista'];
    $id = $_POST['id'];
    $origem = $_POST['origem'];
    $c -> altera_motorista($id, $motorista);
    $c ->set_status($id, 1);
    $sh -> salva_status($id, "Motorista alterado para: " . $m ->get_motorista($motorista)['nome'], $origem);
    echo $id;
}
?>