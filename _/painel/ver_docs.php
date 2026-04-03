<?php
header("Content-type: text/html; charset=utf-8");
include("seguranca.php");
include("../classes/motorista_docs.php");
$md = new motorista_docs();
?>
<!doctype html>
<html lang="pt-br">
<?php include("head.php"); ?>

<body>
	<div class="container-fluid">
	</div>
	<?php
	include("menu.php");
	$id = $_GET['id'];
	$linha = $md->get_by_id($id);
	$img = array(
		$linha['img_cnh'],
		$linha['img_documento'],
		$linha['img_lateral'],
		$linha['img_frente'],
		$linha['img_selfie'],
	); 
	?>
	<div class="container">
		<div class="container-principal-produtos">
			<hr>
			<div class="row">

				<div class="col-md-4">
					<h4 class="page-header">Detalhes do motorista: </h4>
				</div>
				<div class="col-md-4">
					<a class="btn btn-outline-warning" href="lista_motoristas_temp.php" role="button">VOLTAR</a>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-lg-12">
					<div class="div-center">

						<div class="gallery">
							<?php
							for ($i = 0; $i < count($img); $i++) {
								$imageThumbURL = IMG_DIR . $img[$i];
								$imageURL = IMG_DIR . $img[$i];
							?>
								<a href="<?php echo $imageURL; ?>" data-fancybox="gallery" data-caption="Imagem">
									<img width=150 height=100 src="<?php echo $imageThumbURL; ?>" alt="" />
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<hr>
			<?php if (mysqli_num_rows($resultado) > 0) { ?>
				<div class="col-md-8">
					<b>Nome: </b><?php echo $linha['nome']; ?><br>
					<b>CPF: </b><?php echo $linha['cpf']; ?><br>
					<b>Telefone: </b><?php echo $linha['telefone']; ?><br>
					<b>Veículo: </b><?php echo $linha['veiculo']; ?><br>
					<b>Placa: </b><?php echo $linha['placa']; ?><br>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- JavaScript -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://demos.codexworld.com/includes/js/bootstrap.js"></script>
	<!-- Fancybox CSS library -->
	<link rel="stylesheet" href="fancybox/jquery.fancybox.css">
	<!-- Fancybox JS library -->
	<script src="fancybox/jquery.fancybox.js"></script>
	</head>
	</div>
	<?php include("dep_query.php"); ?>
	<script>
		$("[data-fancybox]").fancybox();
	</script>
</body>

</html>