<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include ("../classes/corridas.php");
include ("../classes/avaliacoes.php");

$c = new Clientes();
$m = new corridas();
$a = new avaliacoes();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cliente = $c ->login($telefone, $senha);
if($cliente){
    
    //verifica se o cliente já avaliou essa corrida
    $corrida_id = $_POST['corrida_id'];
    if($a ->verifica_avaliacao($corrida_id)){
        echo json_encode(array('status' => 'erro', 'mensagem' => 'Você já avaliou essa corrida'));
        exit();
    }

    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];
    $cliente_id = $cliente['id'];
    $corrida = $m-> get_corrida_id($corrida_id);
    $motorista_id = $corrida['motorista_id'];
    $a->insere($cliente_id, $motorista_id, $nota, $comentario, $corrida_id);
    echo json_encode(array('status' => 'ok', 'mensagem' => 'Avaliação inserida com sucesso'));
}

?>