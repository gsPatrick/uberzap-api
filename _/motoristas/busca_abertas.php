<?php
include ("../classes/corridas.php");
include ("../classes/mensagens.php");
include ("../classes/seguranca.php");
include ("../classes/clientes.php");
include ("../classes/tempo.php");
$secret_key= $_POST['secret'];

$s = new seguranca();
if($s->compare_secret($secret_key)){
	$c = new corridas();
	$m = new mensagens();
	$t = new tempo();
	$clientes = new clientes();
	$motorista_id = $_POST['motorista_id'];
	$corridas  = $c -> get_corridas_abertas($motorista_id);
	if($corridas){
		foreach ($corridas as $key => $value) {
			$messages = $m ->get_all_msg($value['id']);
			if($messages){
				$corridas[$key]['msg'] = $messages;
			} else {
				$corridas[$key]['msg'] = "";
			}
			if($value['cliente_id'] != 0){
				$cliente = $clientes->get_cliente_id($value['cliente_id']);
				$corridas[$key]['telefone_cliente'] = $cliente['telefone'];
			} else {
				if($value['user_whatsapp']){
					$corridas[$key]['telefone_cliente'] = substr($value['user_whatsapp'], 2);
				} else {
					$corridas[$key]['telefone_cliente'] = "Indisponível";
				}
			}
			$corridas[$key]['hora'] = $t->data_mysql_para_user($value['date']) . " às " . $t->hora_mysql_para_user($value['date']);
		}
		echo json_encode($corridas);
	}else{
		echo "no";
	}
}
?>