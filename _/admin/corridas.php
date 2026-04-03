<?php
include("seguranca.php");
include("../bd/config.php");
include_once '../classes/corridas.php';
include_once '../classes/buttons.php';
include_once '../classes/tempo.php';
include_once '../classes/status_historico.php';
include_once '../classes/cidades.php';
include_once '../classes/clientes.php';
include_once '../classes/motoristas.php';
$sh = new status_historico();
$t = new tempo();
$b = new buttons();
$p = new corridas();
$c = new cidades();
$cliente = new clientes();
$m = new motoristas();

$dados_cidade = $c->get_dados_cidade($cidade_id);
$pedidos = $p->get_all_corridas_cidade($cidade_id);
$lista_de_refs = array();
$lista_de_ids = array();
$last_id = $p ->get_last_id_corrida($cidade_id);

?>
<!doctype html>
<html lang="pt-br">

<?php include "head.php"; ?>
<?php include("menu.php");
include "../componentes/modal_status.php";
include "../componentes/modal_entregadores.php";
$novo_pedido = 0;
?>
<?php include_once "external/verifica_login.php"; ?>
<body>
	<div class="container-fluid">
	</div>
	<div class="container">
		<div class="container-principal-produtos">
			<div class="row">
			<div class="col-md-4">
				<h4 class="page-header"><?php echo $dados_cidade['nome']; ?></h4>
			</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-8">
					<h4 class="page-header">Lista de Corridas em Andamento </h4>
				</div>
			</div>
			<!--Controlador de tamanho e margem da tabela-->
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<th>Nº da Corrida:</th>
						<th>Horario:</th>
						<th>Cliente:</th>
                        <th>Motorista:</th>
						<th>Ações:</th>
						<th>Status</th>
					</thead>
					<tbody>
						<?php
						foreach ($pedidos as $linha) {
							if($linha['status_pagamento'] == 'PENDING'){
								$status_pagamento = '<span class="badge bg-danger">Pendente</span>';
							}else if($linha['status_pagamento'] == 'Autorizado'){
								$status_pagamento = '<span class="badge bg-success">Autorizado</span>';
							}else if($linha['status_pagamento'] == 'Aprovado'){
								$status_pagamento = '<span class="badge bg-success">Aprovado</span>';
							}
							$lista_de_refs[] = $linha['ref'];
							$lista_de_ids[] = $linha['id'];
							echo '<tr>';
							echo  '<td>' . $linha['id'] . '</td>';
							echo  '<td>' . $t ->data_mysql_para_user($linha['date']) . " " . $t ->hora_mysql_para_user($linha['date']) . '</td>';
							echo  '<td>' . $linha['nome_cliente'] . '</td>';
                            echo $linha['motorista_id'] != 0 ? '<td>' . $m->get_motorista($linha['motorista_id'])['nome'] . '</td>' : '<td>Não Atribuido</td>';
							//Ações                                      
							echo  "<td>&nbsp<button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
							echo "<button  class='btn btn-outline-primary' onclick='st_id($linha[id])' data-toggle='modal' data-target='#modal-entregadores'><i class='bi bi-car-front-fill'></i></button ></td>";
							//status
							if ($linha['status_pagamento'] == 'PENDING') {
								echo "<td><button onclick='st_id($linha[id])' class='" . $b->button_status(9) . "' data-toggle='modal' data-target='#modal-status'>Aguarda Pagamento</button>";
							} else {
								echo "<td><button onclick='st_id($linha[id])' class='" . $b->button_status($linha['status']) . "' data-toggle='modal' data-target='#modal-status'>" . $p->status_string($linha['status']) . "</button>";
							}

							echo '</tr>';
						?>
							<!--Inicio Modal.-->
							<div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<center>
												<h3 class="modal-title" id="myModalLabel"> Corrida ID: <?php echo $linha['id']; ?></h3>
											</center>
										</div>
										<div class="modal-body">
											<?php $cupom = $linha['cupom']; ?>
											<b>Data: </b><?php echo $t->data_mysql_para_user($linha['date']) . " " . $t->hora_mysql_para_user($linha['date']); ?><br>

											<b>Nome: </b><?php echo $linha['nome_cliente'] == "" ? "Não Cadastrado" : $linha['nome_cliente']; ?><br>
											<b>Telefone: </b><?php echo $cliente->get_cliente_id($linha['cliente_id']) ? $cliente->get_cliente_id($linha['cliente_id'])['telefone'] : "Não Cadastrado"; ?><br>
											<b>--------------------------------</b><br>

											<b>Endereço Partida: </b><?php echo $linha['endereco_ini_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['endereco_ini_txt']; ?>" target="_blank">Ver no mapa</a><br>
                                            <br>
											<b>Endereço Destino: </b><?php echo $linha['endereco_fim_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['endereco_fim_txt']; ?>" target="_blank">Ver no mapa</a><br>
                                            <b>--------------------------------</b><br>
											<b>Distância: </b><?php echo $linha['km']; ?> km<br>
                                            <b>Tempo: </b><?php echo $linha['tempo']; ?> minutos<br>
											<b>Taxa: R$ </b><?php echo $linha['taxa']; ?><br>

											<b>Forma de Pagamento: </b><?php echo $linha['f_pagamento']; ?><br>
											<b>--------------------------------</b><br>
											<?php
											if ($linha['f_pagamento'] == "ONLINE") { ?>
												<b>Status do Pagamento: </b><?php echo $linha['status_pagamento']; ?><br>
											<?php } ?>
											<?php
											if ($cupom != "0" && $cupom != '') { ?>
												<b>Cupom Usado: </b><?php echo $cupom; ?><br>
											<?php } ?>
											<b>Referência: </b><?php echo $linha['ref']; ?><br>
											<hr>
											<b>Atualizações: </b><br>
											<?php
											$atualizacoes = $sh->get_status($linha['id']);
											$total = count($atualizacoes);
											$i = 0;
											if ($total > 0) {
												foreach ($atualizacoes as $atualizacao) {
													$i++;
													$txt = $atualizacao['hora'] . " - " . $atualizacao['status'];
													$link_maps  = "https://maps.google.com/?q=" . $atualizacao['local'];
													if($atualizacao['local'] != ""){
														$txt .= " - " . "<a href='$link_maps' target='_blank'>Ver no mapa</a>";
													}
													if ($total == $i) {
														$atualizacao_texto = "<b style='color:green'>" . $txt . "</b><br>";
													} else {
														$atualizacao_texto = $txt . "<br>";
													}
													echo $atualizacao_texto;
												}
											}
											?>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
										</div>
									</div>
								</div>
							</div>
							<!--fim modal-->
						<?php
						}
						if ($novo_pedido > 0) { //notifica 
						?>
							<audio id="audio" autoplay>
								<source src="uploads/alerta.mp3" type="audio/mp3" />
							</audio>
							<!--<script>alert('Novo pedido recebido!');</script>-->
						<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!--Fechando container bootstrap-->
	<?php include("dep_query.php"); ?>
	<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
</body>

</html>
<script type="text/javascript">
	var root = '<?php echo DOMINIO; ?>' + "/_/";
    var cidade_id = '<?php echo $cidade_id; ?>';
    

	setTimeout(function() {
		window.location.href = "corridas.php";
	}, 120000); //tempo que a página recarrega em milisegundos

	//var root = "http://localhost/_/";
	var id_pedido = 0;
	var lista_de_refs = '<?php echo json_encode($lista_de_refs); ?>';
	var lista_de_ids = '<?php echo json_encode($lista_de_ids); ?>';
    var last_id = '<?php echo $last_id; ?>';

    lista_de_ids = JSON.parse(lista_de_ids);
    

	function st_id(id) {
		id_pedido = id;
	}

	function muda_status_pedido($id, $status) {
		event.preventDefault();
		$.ajax({
			url: root + 'funcoes/muda_status_corrida.php',
			type: 'POST',
			data: {
				id: $id,
				status: $status,
				origem: 'Painel Franqueado'
			},
			success: function(data) {
				//console.log(data);
				Swal.fire('Status alterado!', '', 'success')
				setTimeout(function() {
					window.location.href = "corridas.php";
				}, 1000);
			},
			error: function(data) {
				//console.log(data);
			}
		});
	}
	$('#btn-alterar-status').on('click', function() {
		let status = $('#status').val();
		muda_status_pedido(id_pedido, status);
	});

	if (localStorage.getItem('alerta') != null) {
		var audio = new Audio('uploads/alerta.mp3');
		audio.play();
		localStorage.removeItem('alerta');
	}


	//funcao buscar alertas
	function buscar_alertas() {
		$.ajax({
			url: root + 'funcoes/get_alertas.php',
			type: 'POST',
			data: {
				cidade_id: cidade_id,
                last_id: last_id
			},
			success: function(data) {
				//console.log(data);
				if (data == '1') {
					localStorage.setItem('alerta', '1');
					setTimeout(function() {
						window.location.href = "corridas.php";
					}, 500);
				}
			},
			error: function(data) {
				//console.log(data);
			}
		});
	}
	setInterval(buscar_alertas, 5000); // busca alertas a cada 5 segundos


	function get_pagamentos() {
		$.ajax({
			url: root + 'funcoes/get_pagamentos.php',
			type: 'POST',
			data: {
				cidade_id: cidade_id
			},
			success: function(data) {
				console.log(data);
				if (data != '0') {
					setTimeout(function() {
						window.location.href = "pedidos.php";
					}, 500);
				}
			},
			error: function(data) {
				//	console.log(data);
			}
		});
	}

	$('#btn-alterar-entregador').on('click', function() {
		let entregador = $('#entregador').val();
		console.log(entregador);
		altera_entregador(id_pedido, entregador);
	});

	function altera_entregador(id_pedido, motorista) {
		event.preventDefault();
		$.ajax({
			url: root + 'funcoes/altera_motorista.php',
			type: 'POST',
			data: {
				id: id_pedido,
				motorista: motorista,
				origem: 'Central'
			},
			success: function(retorno) {
				console.log(retorno);
				Swal.fire('Motorista Alterado!', '', 'success')
				setTimeout(function() {
					window.location.href = "corridas.php";
				}, 1000);
			},
			error: function(data) {
				console.log(data);
			}
		});

	}

	setInterval(get_pagamentos, 45000);

</script>
