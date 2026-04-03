<?php
include("seguranca.php");
include("../bd/config.php");
include "../classes/corridas.php";
include "../classes/motoristas.php";
include "../classes/clientes.php";

$cr = new corridas();
$m = new motoristas();
$c = new clientes();

$date_from = date('Y-m') . "-01 00:00:00";
$date_to =  date('Y-m') . "-30 23:59:59";
$date_inicio_ano = date('Y') . "-01-01 00:00:00";
$corridas_mes = $cr->get_corridas_cidade_datas($cidade_id, $date_from, $date_to, true);
$corridas_ano = $cr->get_corridas_cidade_datas($cidade_id, $date_inicio_ano, $date_to, true);
$corridas_mes_todos = $cr->get_corridas_cidade_datas($cidade_id, $date_from, $date_to, false);
$valor_faturamento_mensal = 0;
$valor_faturamento_ano = 0;
$corridas_feitas_mes = 0;
$corridas_canceladas_mes = 0;
$lista_motoristas_mes = array();
foreach ($corridas_mes as $corrida) {
    $taxa = $corrida['taxa'];
    //verifica se o corridador não está na lista
    if (!in_array($corrida['motorista_id'], $lista_motoristas_mes)) {
        if ($corrida['motorista_id'] != 0) {
        $lista_motoristas_mes[] = $corrida['motorista_id'];
        }
    }
    $taxa = str_replace(',', '.', $taxa);
    if (is_numeric($taxa)) {
        $valor_faturamento_mensal += $m->get_valor_taxa($taxa, $corrida['motorista_id']);
    }
}
foreach ($corridas_ano as $corrida) {
    $taxa = $corrida['taxa'];
    $taxa = str_replace(',', '.', $taxa);
    if (is_numeric($taxa)) {
        $valor_faturamento_ano += $m->get_valor_taxa($taxa, $corrida['motorista_id']);
    }
}
foreach ($corridas_mes_todos as $corrida) {
    $status = $corrida['status'];
    if ($status == 4) {
        $corridas_feitas_mes++;
    } else if ($status == 5) {
        $corridas_canceladas_mes++;
    }
}

$total_motoristas_ativos = $m->get_motoristas($cidade_id, true);
//calcula a porcentagem de motoristas ativos com relação a todos os motoristas
if (count($total_motoristas_ativos) == 0) {
    $porcentagem_motoristas_ativos = 0;
} else {
    $porcentagem_motoristas_ativos = (count($lista_motoristas_mes) / count($total_motoristas_ativos)) * 100;
}
//arreronda o valor para inteiro
$porcentagem_motoristas_ativos = round($porcentagem_motoristas_ativos, 0);

$clientes = $c->get_clientes_cidade($cidade_id);
if($clientes == false){
    $clientes = array();
}else{
    $total_clientes = count($clientes);
}




$meses_array = array(
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro',
);
$corridas = $cr->get_corridas_cidade_datas($cidade_id, $date_inicio_ano, $date_to, true);
$motoristas = $m->get_motoristas($cidade_id);
$dados_lucro = array();
$dados_lucro[] = array('Mês', 'Faturamento', 'Corridas');
$meses_verificados = array();
//verifica mes a mes o total de pedids no campo corrida
foreach ($corridas as $corrida) {
    $data_corrida = $corrida['date'];
    $mes_corrida = date('m', strtotime($data_corrida));
    if (!in_array($mes_corrida, $meses_verificados)) {
        $meses_verificados[] = $mes_corrida;
        $total_corridas_valor = 0;
        $numero_corridas_total = 0;
        foreach ($corridas as $corrida) {
            $data_corrida = $corrida['date'];
            $mes_local = date('m', strtotime($data_corrida));
            if ($mes_local == $mes_corrida) {
                $taxa = $corrida['taxa'];
                $numero_corridas_total++;
                $taxa = str_replace(',', '.', $taxa);
                if (is_numeric($taxa)) {
                    $total_corridas_valor += $taxa;
                    //arreronda o valor para duas casas decimais
                    $total_corridas_valor = round($total_corridas_valor, 2);
                }
            }
        }
        $dados_lucro[] = array($meses_array[$mes_corrida], $total_corridas_valor, $numero_corridas_total);
    } else {
        continue;
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">

    <title> <?php echo $app_name; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Lista de Pedidos">
    <!--LINK CSS-->
    <link rel="stylesheet" type="text/css" href="../css/style-css.css">
    <!--LINK CDN BOOTSTRAP-->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<?php include("menu.php");
?>

<html>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    let dados_lucro = <?php echo json_encode($dados_lucro); ?>;
   
    let corridas_feitas_mes = <?php echo $corridas_feitas_mes; ?>;
    let corridas_canceladas_mes = <?php echo $corridas_canceladas_mes; ?>;
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(dados_lucro);

        var options = {
            title: 'Faturamento Bruto Corridas',
            subtitle: 'e corridas',
            legend: {
                position: 'bottom'
            }
        };

        var chart = new google.visualization.LineChart(document.getElementById('line_top_x'));

        chart.draw(data, options);

        var data_pie = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Corridas Feitas', corridas_feitas_mes],
            ['Corridas Canceladas', corridas_canceladas_mes],
        ]);

        var options_pie = {
            title: 'Corridas Feitas e Canceladas',
            is3D: true,
        };

        var chart_pie = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart_pie.draw(data_pie, options_pie);


    }


</script>

<body>
    <div class="container-grafico">
        <div class="row">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Faturamento Lq (Mensal)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?php echo number_format($valor_faturamento_mensal, 2, ',', '.'); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Annual) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Faturamento (Anual)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$ <?php echo number_format($valor_faturamento_ano, 2, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Motoristas Ativos (Mês)
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $porcentagem_motoristas_ativos; ?>%</div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $porcentagem_motoristas_ativos; ?>%" aria-valuenow="<?php echo $porcentagem_motoristas_ativos; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Clientes Cadastrados</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_clientes; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h4 class="page-header">Faturamento Bruto e Corridas</h4>
                <div style="height: auto; margin-top: 30px; text-align: center;">
                    <div id="line_top_x" style="border: 1px solid #ccc; height: 300px; width: 100%;"></div>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="page-header">Corridas Concluidas e Canceladas</h4>
                <div style="height: auto; margin-top: 30px; text-align: center;">
                    <div id="piechart_3d" style="border: 1px solid #ccc; height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    




</body>

</html>

<?php include("dep_query.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
<script>
</script>