<?php 
header('access-control-allow-origin: *');
include_once "../classes/clientes.php";
include_once "../classes/transacoes.php";
include_once "../classes/tempo.php";

$c = new Clientes();
$tmp = new tempo();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cliente = $c ->login($telefone, $senha);
if($cliente){
    $cidade_id = $cliente['cidade_id'];
    $t = new Transacoes($cidade_id); 
    $cliente_id = $cliente['id'];
    $saldo = $cliente['saldo'];
    $transacoes = $t->getByUserId($cliente_id);
    $transacoes = array_reverse($transacoes);
    foreach($transacoes as $key => $transacao){
        $transacoes[$key]['date'] = $tmp ->data_mysql_para_user($transacao['date']) . " " . $tmp->hora_mysql_para_user($transacao['date']);
    }
    $dados_retorno = array(
        'saldo' => $saldo,
        'transacoes' => $transacoes
    );
    echo json_encode($dados_retorno);
}


?>