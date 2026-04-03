<?php
include("seguranca.php");
include("../bd/config.php");
include_once '../classes/corridas.php';
include_once '../classes/franqueados.php';

$cr = new corridas();
$f = new franqueados();



include("seguranca.php");
include_once "head.php";
include("menu.php");
include_once "external/verifica_login.php";
if (empty($_GET['date_from'])) {

	$date_from = date('Y-m') . "-01 00:00:00";
	$date_to =  date('Y-m') . "-30 23:59:59";
} else {
	$date_from = $_GET['date_from'] . " 00:00:00";
	$date_to =  $_GET['date_to'] . " 23:59:59";
}
$corridas = $cr->get_corridas_cidade_datas($cidade_id, $date_from, $date_to, true);
$usuarios = $f->get_usuarios_cidade($cidade_id);
$comissao = $usuarios[0]['comissao'];

?>

<body>
	<div class="container-principal-produtos">
		<div class="container">
			<br>
			<div class="row">
				<h4 class="page-header">Pesquisar entre:</h4>
				<div class="form-group col-md-2">
					<form action="relatorio_resumido.php" method="GET" enctype="multipart/form-data" name="upload">
						<input type="date" id="date_from" value="<?php echo substr($date_from, 0, 10); ?>" name="date_from">
				</div>
				<div class="form-group col-md-1">
					<h4 class="page-header"> Até </h4>
				</div>
				<div class="form-group col-md-2">
					<input type="date" id="date_to" value="<? echo substr($date_to, 0, 10); ?>" name="date_to">
				</div>
				<div class="form-group col-md-2">
					<input type="submit" class="btn btn-primary" name="btn_enviar" value="Pesquisar">
				</div>
				</form>
			</div>
			<hr>


			<div class="row">
				<div class="col-6">
					<form action="exportar_resumido.php?date_from=<? echo $date_from; ?>&date_to=<? echo $date_to; ?>" method="POST" enctype="multipart/form-data" name="upload">
						<h4 class="page-header">Relatório entre as datas selecionadas</h4>
				</div>
			</div>
			</form>
			<br>
			<div class="row">
				<div class="col-6">
					<h4 class="page-header">Total de corridas: <?php echo count($corridas); ?></h4>
					<h4 class="page-header">
						<span id="valor_total_corridas"></span>
					</h4>
					<h4 class="page-header">
						<span id="valor_total"></span>
					</h4>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<th>Data</th>
						<th>Corridas</th>
						<th>Valor Total</th>
						<th>Comissão</th>
					</thead>
					<tbody>
						<?php
						$valor_total_comissao = 0;
						$valor_total_corridas = 0;
						//pega o total de dias entre o periodo selecionado
						$total_de_dias = (strtotime($date_to) - strtotime($date_from)) / 86400;
						//para cada dia, pega o total de corridas e o valor total
						for ($i = 0; $i <= $total_de_dias; $i++) {
							$dia = date('Y-m-d', strtotime($date_from . ' + ' . $i . ' days'));
							$corridas_dia = $cr->get_corridas_cidade_datas($cidade_id, $dia . " 00:00:00", $dia . " 23:59:59", true);
							$valor_total_dia = 0;
							$comissao_dia = 0;

							foreach ($corridas_dia as $corrida) {
								$valor_total_dia += str_replace(',', '.', $corrida['taxa']);
							}
							$comissao_dia = $valor_total_dia * ($comissao / 100);
							$porcentagem = $comissao_dia / $valor_total_dia * 100;
							$valor_total_comissao += $comissao_dia;
							$valor_total_corridas += $valor_total_dia;

						?>
							<tr>
								<td><?php echo implode('/', array_reverse(explode('-', $dia))); ?></td>
								<td><?php echo count($corridas_dia); ?></td>
								<td><?php echo $valor_total_dia; ?></td>
								<td><?php echo number_format($comissao_dia, 2, ',', '.'); ?></td>
							</tr>
						<?php
						}
						?>
			</div>
		</div>
	</div>
</body>

</html>
<?php include("dep_query.php"); ?>

</html>
<script>
	var valor_total = "<?php echo number_format($valor_total_comissao, 2, ',', '.'); ?>";
	var valor_total_corridas = "<?php echo number_format($valor_total_corridas, 2, ',', '.'); ?>";
	document.getElementById("valor_total").innerHTML = "Valor total de comissão: R$ " + valor_total;
	document.getElementById("valor_total_corridas").innerHTML = "Valor total de corridas: R$ " + valor_total_corridas;
</script>