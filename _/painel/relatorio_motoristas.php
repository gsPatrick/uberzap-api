<?
include("seguranca.php");
include "../classes/cidades.php";
include "../classes/motoristas.php";
include "../classes/corridas.php";
$p = new corridas();
$c = new cidades();
$e = new motoristas();
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>
<?php
if (empty($_POST['date_from'])) {
	$date_from = date('Y-m') . "-01 00:00:00";
	$date_to =  date('Y-m') . "-30 23:59:59";
	$data_from_view = date('Y-m') . "-01";
	$data_to_view =  date('Y-m') . "-30";
} else {
	$date_from = $_POST['date_from'] . " 00:00:00";
	$date_to =  $_POST['date_to'] . " 23:59:59";
	$data_from_view = $_POST['date_from'];
	$data_to_view =  $_POST['date_to'];
}
?>

</head>

<body>
	<div class="container-principal-produtos">
		<div class="container">
			<br>
			<div class="row">
				<h4 class="page-header">Pesquisar entre:</h4>
				<div class="form-group col-md-2">
					<form action="relatorio_motoristas.php" method="POST" enctype="multipart/form-data" name="upload">
						<input type="date" id="date_from" value="<? echo $data_from_view; ?>" name="date_from">
				</div>
				<div class="form-group col-md-1">
					<h4 class="page-header"> Até </h4>
				</div>
				<div class="form-group col-md-2">
					<input type="date" id="date_to" value="<? echo $data_to_view; ?>" name="date_to">
				</div>
				<div class="form-group col-md-2">
					<input type="submit" class="btn btn-primary" name="btn_enviar" value="Pesquisar">
				</div>
				</form>
			</div>
			<br>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<th>#</th>
						<th>Motorista</th>
						<th>Corridas</th>
						<th>Saldo</th>
						<th>Comissão Plataforma</th>
						<th>Valor Total Corridas</th>
					</thead>
					<tbody>
						<?php
						$num = 0;
						$motoristas = $e->get_motoristas($cidade_id);
						foreach ($motoristas as $motorista) {
							$corridas = $p->get_corridas_motorista_datas($motorista['id'], $date_from, $date_to, true);
							$total_corridas = 0;
							$total_valor = 0;
							$valor_taxa = 0;
							$valor_comissao = 0;
							$porcentagem_entregador  = $motorista['taxa'];
							foreach ($corridas as $corrida) {
								$total_corridas++;
								$valor_corrida = str_replace(',', '.', $corrida['taxa']);
								$total_valor += $valor_corrida;
								$valor_taxa += str_replace(',', '.', $corrida['taxa']);
							}
							//calcula valor_comissao
							$valor_comissao = $valor_taxa * $porcentagem_entregador / 100;
							$total_pagar = $valor_taxa - $valor_comissao;
							$num++;

							echo '<tr>';
							echo  '<td>' . $num . '</td>';
							echo  '<td>' . $motorista['nome'] . '</td>';
							echo  '<td>' . $total_corridas . '</td>';
							echo  '<td>' . number_format($motorista['saldo'], 2, ',', '.') . '</td>';
							echo  '<td>' . number_format($valor_comissao, 2, ',', '.') . '</td>';
							echo  '<td>' . number_format($valor_taxa, 2, ',', '.') . '</td>';
							echo '</tr>';



							echo '<tr>';
						} //while dados_motorista
						?>
			</div>
		</div>
	</div>
</body>

</html>
<?php include("dep_query.php"); ?>

</html>