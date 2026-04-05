<?php
include ("../classes/seguranca.php");
include ("../classes/corridas.php");
include ("../classes/status_historico.php");

$secret_key= $_POST['secret'];

$s = new seguranca();     
if($s->compare_secret($secret_key)){
    $c = new corridas();
    $sh = new status_historico();

    $id_corrida = $_POST['id_corrida'];
    $acao = $_POST['acao']; // 'iniciar' ou 'finalizar'

    if($acao == 'iniciar'){
        $res = $c->setInicioEspera($id_corrida);
        $sh->salva_status($id_corrida, "Iniciou tempo de espera", "App Motorista");
        echo $res ? "ok" : "erro";
    } else if($acao == 'finalizar'){
        $res = $c->finalizaEspera($id_corrida);
        $sh->salva_status($id_corrida, "Finalizou tempo de espera", "App Motorista");
        echo $res ? "ok" : "erro";
    }
}
?>
