<?php
include("../classes/seguranca.php");
include("../classes/corridas.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");
include("../classes/transacoes_motoristas.php");


$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
    $id_cidade = $_POST['id_cidade'];
    $c = new corridas();
    $sh = new status_historico();
    $m = new Motoristas();
    $tm = new transacoes_motoristas($id_cidade);


    $id_motorista = $_POST['id_motorista'];
    $taxa = $_POST['taxa'];
    $taxa = str_replace(',', '.', $taxa);
    $tempo = $_POST['tempo'];
    $km = $_POST['km'];
    $endereco_fim = $_POST['endereco_fim'];
    $id_corrida = $_POST['id_corrida']; 

    //verifica taxa do motorista
    $dados_motorista = $m->get_motorista($id_motorista);
    $taxa_porcentagem = $dados_motorista['taxa'];
    $saldo_motorista = $dados_motorista['saldo'];
    $taxa_motorista = $taxa_porcentagem * $taxa / 100;
    $novo_saldo = $saldo_motorista - $taxa_motorista;
    $novo_saldo = number_format($novo_saldo, 2, ',', '');
    $m -> atualiza_saldo($id_motorista, $novo_saldo);

    $taxa_motorista = number_format($taxa_motorista, 2, ',', '');
    $tm -> insereTransacao($id_motorista, 'N/A', $taxa_motorista, 'DEBITO TAXIMETRO', 'CONCLUIDO');

    $c ->update_taximetro($id_corrida, $taxa, $tempo, $km, $endereco_fim);
    $c ->set_status($id_corrida, 4);
    $sh -> salva_status($id_corrida, "Taxímetro finalizado", "App Motorista");
    echo "ok";
}
