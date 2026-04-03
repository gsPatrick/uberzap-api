<?php
include ("../classes/cidades.php");
include ("../classes/seguranca.php");
$secret_key= $_POST['secret'];
$s = new seguranca();
if($s->compare_secret($secret_key)){
	$c = new Cidades();
	$cidades = $c->get_array_cidades();
	$dados = array();
	foreach ($cidades as $cidade) {
		$dados[] = array(
			'id' => $cidade['id'],
			'nome' => $cidade['nome']
		);
	}
	echo json_encode($dados);
}else{
	echo "no";
}
?>