<?php
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/status_historico.php");
include("../classes/motoristas.php");

$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
	$id_corrida = $_POST['id_corrida'];
	$c = new corridas();
	$sh = new status_historico();
	$m = new Motoristas();
	$c ->set_status($id_corrida, 0);
	$corrida = $c->get_corrida_id($id_corrida);
	$dados_motorista = $m->get_motorista($corrida['motorista_id']);
	$nome_motorista = $dados_motorista['nome'];
	$sh ->salva_status($id_corrida, $nome_motorista . " cancelou a corrida", "App Motorista");
	echo "ok";
}
?>