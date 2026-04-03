<?
include("seguranca.php");
include("../classes/motoristas.php");
include("../classes/categorias.php");
$m = new motoristas();
$motorista_id = $_GET['id'];
$dados_motorista = $m->get_motorista($motorista_id);
$c = new categorias();
$categorias = $c->get_categorias($cidade_id);
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
	<div class="container-fluid">
	</div>
	<div class="container">
		<div class="container-principal-produtos">
			<h4 class="page-header">EDITAR INFORMAÇÕES DO MOTORISTA</h4>
			<hr>
			<form action="edit_motorista.php?id=<?php echo $motorista_id ?>" method="POST" enctype="multipart/form-data" name="upload">
				<div class="row">
				</div>
				<div class="row">
					<div class="form-group col-md-8">
						<!--Realizando Upload de Imagem-->
						<label class="control-label">Foto do Motorista</label>
						<input class="form-control" type="file" name="img">
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-8">
						<label>Nome do motorista:</label>
						<input class="form-control form-control-sm col-md-09 col-sm-09" value="<?php echo $dados_motorista['nome']; ?>" type="text" name="nome" placeholder="Nome" />
						</br>
						<label>e-mail:</label>
                        <input class="form-control form-control-sm col-md-09 col-sm-09" value="<?php echo $dados_motorista['email']; ?>"type="text" name="email" placeholder="email" />
                        </br>
						<label>Telefone:</label>
						<input class="form-control form-control-sm col-md-09 col-sm-09" value="<?php echo $dados_motorista['telefone']; ?>" type="text" name="telefone" placeholder="Telefone" />
						</br>
						<label>CPF do motorista:</label>&nbsp
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['cpf']; ?>" name="cpf" placeholder="CPF" />
						<br>
						<label>Senha para acesso do motorista:</label>&nbsp
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['senha']; ?>" name="senha" placeholder="Senha" />
						<br>
						<label>Veículo do motorista:</label>
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['veiculo']; ?>" name="veiculo" placeholder="Veículo" />
						<br>
						<label>Placa do veículo:</label>&nbsp
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['placa']; ?>" name="placa" placeholder="Placa" />
						<br>
						<label>Taxa a cobrar em porcentagem:</label>&nbsp
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['taxa']; ?>" name="taxa" placeholder="Taxa" />
						<br>
						<label>Saldo:</label>&nbsp
						<input class="form-control form-control-sm col-md-09 col-sm-09" type="text" value="<?php echo $dados_motorista['saldo']; ?>" name="saldo" placeholder="Saldo" />
						<br>
						<div class="row">
							<div class="form-group col-md-4">
								<label>Categorias:</label>
								<div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
								<?php	
								$ids_categorias = json_decode($dados_motorista['ids_categorias']);
									if ($ids_categorias === NULL) {
									$ids_categorias = array();
									}
									foreach ($categorias as $c) {
									$checked = "";
									if (in_array($c['id'], $ids_categorias)) {
									$checked = "checked";
									}
									echo '<input type="checkbox" name="ids_categorias[]" value="' . $c['id'] . '" ' . $checked . '> ' . $c['nome'] . '<br>';
									}
								?>
								</div>

							</div>
						</div>


						<input type="submit" class="btn btn-primary" name="btn_enviar" value="Atualizar informações">
						<hr>
			</form>
		</div>
	</div><!--Fechando container bootstrap-->
	<?php include("dep_query.php"); ?>
</body>

</html>