<?php
include ("../classes/seguranca.php");
include ("../classes/corridas.php");
include ("../classes/status_historico.php");


$secret_key= $_POST['secret'];

$s = new seguranca();     
if($s->compare_secret($secret_key)){
    $c = new corridas();
    $sh = new status_historico();

 $id_motorista = $_POST['id_motorista'];
 $taxa = $_POST['taxa'];
 $tempo = $_POST['tempo'];
 $km = $_POST['km'];
 $endereco_ini = $_POST['endereco_ini'];
 $id_cidade = $_POST['id_cidade'];

    $id_corrida = $c -> insere_corrida($id_motorista, 0, $id_cidade, 0, 0, 0, 0, $km, $tempo, $endereco_ini, "", $taxa, 0, 0, "", "", 0);
    $c ->set_status($id_corrida, 9);
    $sh -> salva_status($id_corrida, "Taxímetro iniciado", "App Motorista");
    echo $id_corrida;
}


?>