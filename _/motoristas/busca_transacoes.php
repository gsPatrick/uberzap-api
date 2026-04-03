<?php
header('access-control-allow-origin: *');
include_once "../classes/motoristas.php";
include_once "../classes/transacoes_motoristas.php";
include_once "../classes/tempo.php";

$m = new Motoristas();
$tmp = new tempo();

$motorista_id = $_POST['motorista_id'];
$cidade_id = $cliente['cidade_id'];

$t = new transacoes_motoristas($cidade_id);

$dados_motorista = $m->get_motorista($motorista_id);
$saldo = $dados_motorista['saldo'];

$transacoes = $t->getByUserId($motorista_id);
if($transacoes != false){
    foreach ($transacoes as $key => $transacao) {
        $transacoes[$key]['date'] = $tmp->data_mysql_para_user($transacao['date']) . " " . $tmp->hora_mysql_para_user($transacao['date']);
    }
}

$dados_retorno = array(
    'saldo' => $saldo,
    'transacoes' => $transacoes
);
echo json_encode($dados_retorno);
