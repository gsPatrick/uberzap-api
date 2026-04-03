<?php
include("../bd/conexao.php");
date_default_timezone_set('America/Sao_Paulo');
$id_motorista = $_POST['id_motorista'];
$secret_key = $_POST['secret'];
$id_cidade = $_POST['id_cidade'];
$date =  date('Y-m-d') . " 00:00:00";
$hoje_ini = date('Y-m-d') . " 00:00:00";
$hoje_fim = date('Y-m-d') . " 23:59:59";
//pega primeiro dia da semana no formato datetime
$datetime_semana = date('Y-m-d', strtotime('monday this week'));
$datetime_semana = $datetime_semana . " 00:00:00";
//pega o primeiro dia do mes no formato datetime
$datetime_mes = date('Y-m-01');
$datetime_mes = $datetime_mes . " 00:00:00";
//pega o primeiro dia do ano no formato datetime
$datetime_ano = date('Y-01-01');
$datetime_ano = $datetime_ano . " 00:00:00";
$mes = date('m');
$ano = date('Y');

$total_valor_hoje = 0;
$total_valor_semana = 0;
$total_valor_mes = 0;
$total_valor_todo = 0;

$total_hoje = 0;
$total_semana = 0;
$total_mes = 0;
$total_fim = 0;

if ($secret == $secret_key) { // verifica se a chave enviada pelo app é igual a do arquivo conexao.php

    $busca_ent = "SELECT * FROM motoristas WHERE id = '$id_motorista';";
    $sql_ent = mysqli_query($conexao, $busca_ent);
    $dados_ent = mysqli_fetch_assoc($sql_ent);
    $taxa_plataforma = $dados_ent['taxa'];
    $saldo = $dados_ent['saldo'];


    $busca = "SELECT * FROM corridas WHERE motorista_id = '$id_motorista'
    AND (status = '4' OR status = '9') 
    AND date BETWEEN '$hoje_ini' AND '$hoje_fim'";
    $resultado = mysqli_query($conexao, $busca);
    $total_hoje  = mysqli_num_rows($resultado);
    while ($array = mysqli_fetch_assoc($resultado)) {
        $taxa_hoje = str_replace(',', '.', $array['taxa']);
        $total_valor_hoje = $total_valor_hoje + $taxa_hoje;
    }

    $busca2 = "SELECT * 
    FROM corridas 
    WHERE motorista_id = '$id_motorista'
        AND (status = '4' OR status = '9') 
        AND date BETWEEN '$datetime_semana' AND '$hoje_fim'
    ";
    
    $resultado2 = mysqli_query($conexao, $busca2);
    $total_semana  = mysqli_num_rows($resultado2);
    while ($array = mysqli_fetch_assoc($resultado2)) {
        $taxa_semana = str_replace(',', '.', $array['taxa']);
        $total_valor_semana = $total_valor_semana + $taxa_semana;
    }

    $busca3 = "SELECT * 
    FROM corridas 
    WHERE motorista_id = '$id_motorista'
        AND (status = '4' OR status = '9') 
        AND date BETWEEN '$datetime_mes' AND '$hoje_fim'
    ";
    $resultado3 = mysqli_query($conexao, $busca3);
    $total_mes  = mysqli_num_rows($resultado3);
    while ($array = mysqli_fetch_assoc($resultado3)) {
        $taxa_mes = str_replace(',', '.', $array['taxa']);
        $total_valor_mes = $total_valor_mes + $taxa_mes;
    }

    $busca4 = "SELECT * FROM corridas WHERE motorista_id = '$id_motorista'
    AND (status = '4' OR status = '9')";
    $resultado4 = mysqli_query($conexao, $busca4);
    $total_fim  = mysqli_num_rows($resultado4);
    while ($array = mysqli_fetch_assoc($resultado4)) {
        $tx_total = str_replace(',', '.', $array['taxa']);                                
        $total_valor_todo = $total_valor_todo + $tx_total;
    }

    $busca5 = "SELECT * FROM corridas WHERE motorista_id = '$id_motorista' 
    AND (status = '4' OR status = '9') ";
    $resultado5 = mysqli_query($conexao, $busca5);
    while ($array = mysqli_fetch_assoc($resultado5)) {
        $tx = str_replace(',', '.', $array['taxa']);
        $taxa_total = $taxa_total + $tx;
    }


    $taxa_hoje = $total_valor_hoje * $taxa_plataforma / 100;
    $taxa_semana = $total_valor_semana * $taxa_plataforma / 100;
    $taxa_mes = $total_valor_mes * $taxa_plataforma / 100;

    $lucro_hoje = $total_valor_hoje - $taxa_hoje;
    $lucro_semana = $total_valor_semana - $taxa_semana;
    $lucro_mes = $total_valor_mes - $taxa_mes;
    $lucro_total = $total_valor_todo - ($total_valor_todo * $taxa_plataforma / 100);


    $dados = array(
        "qnt_hoje" => $total_hoje,
        "valor_hoje" => number_format($total_valor_hoje, 2, ',', '.'),
        "taxa_hoje" => number_format($taxa_hoje, 2, ',', '.'),
        "lucro_hoje" => number_format($lucro_hoje, 2, ',', '.'),
        "qnt_semana" => $total_semana,
        "valor_semana" => number_format($total_valor_semana, 2, ',', '.'),
        "taxa_semana" => number_format($taxa_semana, 2, ',', '.'),
        "lucro_semana" => number_format($lucro_semana, 2, ',', '.'),
        "qnt_mes" => $total_mes,
        "valor_mes" => number_format($total_valor_mes, 2, ',', '.'),
        "taxa_mes" => number_format($taxa_mes, 2, ',', '.'),
        "lucro_mes" => number_format($lucro_mes, 2, ',', '.'),
        "qnt_fim" => $total_fim,
        "valor_fim" => number_format($lucro_total, 2, ',', '.'),
        "saldo_plataforma" => $saldo
    );

    echo json_encode($dados);
} else {
    echo "no";
}
