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
<?php include("menu.php"); ?>

<?php
// Definição das datas
if (empty($_GET['date_from'])) {
    $date_from = date('Y-m') . "-01";
    $date_to =  date('Y-m') . "-30"; 
} else {
    $date_from = $_GET['date_from'];
    $date_to =  $_GET['date_to'];
}
$datetime_from = $date_from . " 00:00:00";
$datetime_to = $date_to . " 23:59:59";

// Busca de corridas no período
$historico = $p->get_corridas_cidade_datas($cidade_id, $datetime_from, $datetime_to);
$historico = array_reverse($historico);

// Captura filtro por usuário
$usuario = isset($_GET['usuario']) ? trim($_GET['usuario']) : '';

// Se o campo foi preenchido, filtra os resultados
if ($usuario !== '') {
    $usuario = mb_strtolower($usuario, 'UTF-8');
    $historico = array_filter($historico, function($linha) use ($usuario, $m) {
        $nome_cliente = mb_strtolower($linha['nome_cliente'], 'UTF-8');
        $nome_motorista = '';
        if ($linha['motorista_id'] != 0) {
            $motorista = $m->get_motorista($linha['motorista_id']);
            if ($motorista && isset($motorista['nome'])) {
                $nome_motorista = mb_strtolower($motorista['nome'], 'UTF-8');
            }
        }
        return (strpos($nome_cliente, $usuario) !== false || strpos($nome_motorista, $usuario) !== false);
    });
}
?>

<div class="container">
    <div class="container-principal-produtos">
        <div class="row">
    <h4 class="page-header">Pesquisar entre:</h4>
    <form action="historico.php" method="GET" class="d-flex flex-wrap align-items-end" style="gap:10px;">
        <div class="form-group">
            
            <input type="date" class="form-control" value="<?php echo $date_from; ?>" name="date_from">
        </div>

        <div class="form-group">
            <label>Até:</label>
            <input type="date" class="form-control" value="<?php echo $date_to; ?>" name="date_to">
        </div>

        <div class="form-group">
            <label>Usuário:</label>
            <input type="text" class="form-control" placeholder="Buscar por usuário" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>">
        </div>

        <div class="form-group d-flex flex-column">
            <input type="submit" class="btn btn-primary mb-2" name="btn_enviar" value="Pesquisar">
            <a href="historico.php" class="btn" style="background-color:#ff0000; color:#fff;">Limpar</a>
        </div>
    </form>
</div>


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
                    <?php foreach ($historico as $linha) { ?>
                        <tr>
                            <td><?php echo $linha['id']; ?></td>
                            <td><?php echo $t->data_mysql_para_user($linha['date']) . ' - ' . $t->hora_mysql_para_user($linha['date']); ?></td>
                            <td><?php echo $linha['nome_cliente']; ?></td>
                            <td>
                                <?php 
                                if ($linha['motorista_id'] != 0) {
                                    echo $m->get_motorista($linha['motorista_id'])['nome'];
                                } else {
                                    echo "Não Atribuido";
                                }
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal<?php echo $linha['id']; ?>">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                            <td>
                                <button class="<?php echo $b->button_status($linha['status']); ?>">
                                    <?php echo $p->status_string($linha['status']); ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal da corrida -->
                        <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel<?php echo $linha['id']; ?>">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <center><h3 class="modal-title" id="myModalLabel<?php echo $linha['id']; ?>">Corrida ID: <?php echo $linha['id']; ?></h3></center>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php $cupom = $linha['cupom']; ?>
                                        <b>Data: </b><?php echo $t->data_mysql_para_user($linha['date']) . " " . $t->hora_mysql_para_user($linha['date']); ?><br>
                                        <b>Nome: </b><?php echo $linha['nome_cliente']; ?><br>
                                        <b>Telefone: </b><?php echo $cliente->get_cliente_id($linha['cliente_id']) ? $cliente->get_cliente_id($linha['cliente_id'])['telefone'] : "Não Cadastrado"; ?><br>
                                        <b>--------------------------------</b><br>
                                        <b>Endereço Partida: </b><?php echo $linha['endereco_ini_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['lat_ini']; ?>,<?php echo $linha['lng_ini']; ?>" target="_blank">Ver no mapa</a><br>
                                        <b>Endereço Destino: </b><?php echo $linha['endereco_fim_txt']; ?> <a href="https://maps.google.com/?q=<?php echo $linha['lat_fim']; ?>,<?php echo $linha['lng_fim']; ?>" target="_blank">Ver no mapa</a><br>
                                        <b>Observações: </b><?php echo $linha['obs']; ?><br>
                                        <b>Distância: </b><?php echo $linha['km']; ?> km<br>
                                        <b>Tempo: </b><?php echo $linha['tempo']; ?> minutos<br>
                                        <b>Taxa: R$ </b><?php echo $linha['taxa']; ?><br>
                                        <b>Forma de Pagamento: </b><?php echo $linha['f_pagamento']; ?><br>
                                        <?php if ($linha['f_pagamento'] == "ONLINE") { ?>
                                            <b>Status do Pagamento: </b><?php echo $linha['status_pagamento']; ?><br>
                                        <?php } ?>
                                        <?php if ($cupom != "0") { ?>
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
                        <!-- Fim Modal -->
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("dep_query.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.0.js" crossorigin="anonymous"></script>
</body>
</html>
