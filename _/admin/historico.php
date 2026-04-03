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
?>
<!doctype html>
<html lang="pt-br">
<?php include("head.php"); ?>

<body>
    <?php
    include("menu.php");
    include_once "external/verifica_login.php";
    if (empty($_GET['date_from'])) {
        $date_from = date('Y-m') . "-01";
        $date_to =  date('Y-m') . "-30"; 
    } else {
        $date_from = $_GET['date_from'];
        $date_to =  $_GET['date_to'];
    }
    $datetime_from = $date_from . " 00:00:00";
    $datetime_to = $date_to . " 23:59:59";
    $historico = $p->get_corridas_cidade_datas($cidade_id, $datetime_from, $datetime_to);
    $historico = array_reverse($historico);
    ?>

    <div class="container">
        <div class="container-principal-produtos">
            <div class="row">
                <h4 class="page-header">Pesquisar entre:</h4>
                <div class="form-group col-md-2">
                    <form action="historico.php" method="GET" enctype="multipart/form-data" name="upload">
                        <input type="date" value="<? echo $date_from; ?>" id="date_from" name="date_from">
                </div>
                <div class="form-group col-md-1">
                    <h4 class="page-header"> Até </h4>
                </div>
                <div class="form-group col-md-2">
                    <input type="date" value="<? echo $date_to; ?>" id="date_to" name="date_to">
                </div>
                <div class="form-group col-md-2">
                    <input type="submit" class="btn btn-primary" name="btn_enviar" value="Pesquisar">
                </div>
                </form>
            </div>
            <h4 class="page-header"></h4>
            <h4 class="page-header">Lista de corridas entre as datas <?php echo implode('/', array_reverse(explode('-', $date_from))); ?> e <?php echo implode('/', array_reverse(explode('-', $date_to))); ?>:</h4>
            <label class="page-header">Total de corridas: <?php echo count($historico); ?></label>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>#</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Motorista</th>
                        <th>Ações</th>
                        <th>Status</th>

                    </thead>
                    <tbody>
                        <?php
                        foreach ($historico as $linha) {

                            echo '<tr>';
                            echo  '<td>' . $linha['id'] . '</td>';
                            echo  '<td>' . $t->data_mysql_para_user($linha['date']) . ' - ' . $t ->hora_mysql_para_user($linha['date']) . '</td>';
                            echo  '<td>' . $linha['nome_cliente'] . '</td>';
                            echo $linha['motorista_id'] != 0 ? '<td>' . $m->get_motorista($linha['motorista_id'])['nome'] . '</td>' : '<td>Não Atribuido</td>';
                            echo  "<td>&nbsp<button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp</td>";
                            echo  "<td><button  class='" . $b->button_status($linha['status']) . "'>".$p->status_string($linha['status']) ."</button></td>";
                        ?>
                            </tr>
                            <!--Inicio Modal.-->
							<div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<center>
												<h3 class="modal-title" id="myModalLabel"> Corrida ID: <?php echo $linha['id']; ?></h3>
											</center>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</div>
										<div class="modal-body">
											<?php $cupom = $linha['cupom']; ?>
											<b>Data: </b><?php echo $t->data_mysql_para_user($linha['date']) . " " . $t->hora_mysql_para_user($linha['date']); ?><br>

											<b>Nome: </b><?php echo $linha['nome_cliente']; ?><br>
											<b>Telefone: </b><?php echo $cliente->get_cliente_id($linha['cliente_id']) ? $cliente->get_cliente_id($linha['cliente_id'])['telefone'] : "Não Cadastrado"; ?><br>
											<b>--------------------------------</b><br>

											<b>Endereço Partida: </b><?php echo $linha['endereco_ini_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['lat_ini']; ?>,<?php echo $linha['lng_ini']; ?>" target="_blank">Ver no mapa</a><br>
                                            <br>
											<b>Endereço Destino: </b><?php echo $linha['endereco_fim_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['lat_fim']; ?>,<?php echo $linha['lng_fim']; ?>" target="_blank">Ver no mapa</a><br>
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
											if ($cupom != "0") { ?>
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

                        <?php }
                        include("dep_query.php"); ?>
                        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
</body>
</html>