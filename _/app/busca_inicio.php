<?php
header("access-control-allow-origin: *");
require_once __DIR__ . '/../bd/conexao.php';
include(__DIR__ . '/../bd/config.php');
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/clientes.php';

$crr = new corridas();
$c = new Clientes();


$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $cliente_id = $cliente['id'];
    $resposta = array();
    $corridas = $crr->getAllCorridasAbertasCliente($cliente_id, $telefone);
    if ($corridas && count($corridas) > 0) {
        echo "1";
    } else {
        echo "";
    }
}
