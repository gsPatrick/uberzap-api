<?php
include ("../classes/motoristas.php");
include ("../classes/seguranca.php");
include ("../classes/cidades.php");
include ("../classes/taximetro.php");
$secret_key= $_POST['secret'];
$s = new seguranca();
$c = new Cidades();
$t = new Taximetro();
if($s->compare_secret($secret_key)){
	$cpf = $_POST['cpf'];
	$senha = $_POST['senha'];

	$salt = "anjdsn5s141d5";
	$senha = md5($senha.$salt);

	$m = new Motoristas();
	$login = $m ->login_motorista($cpf, $senha);
	if($login){
		$dados_cidade = $c->get_dados_cidade($login['cidade_id']);
		$id = $login['id'];
		if($m ->verifica_se_esta_ativo($id)){
			$latitude_cidade = $dados_cidade['latitude'];
			$longitude_cidade = $dados_cidade['longitude'];
			$login['latitude_cidade'] = $latitude_cidade;
			$login['longitude_cidade'] = $longitude_cidade;

			$taxas = $t->getByCidadeId($login['cidade_id']);
			if($taxas){
				$login['taxi_tx_minima'] = str_replace(',', '.', $taxas['tx_minima']);
				$login['taxi_tx_minuto'] = str_replace(',', '.', $taxas['tx_minuto']);
				$login['taxi_tx_km'] = str_replace(',', '.', $taxas['tx_km']);
			}else{
				$login['taxi_tx_minima'] = 0;
				$login['taxi_tx_minuto'] = 0;
				$login['taxi_tx_km'] = 0;
			}

			echo json_encode($login);
		}else{
			echo "Motorista bloqueado, entre em contato com a central";
		}
	}else{
		echo "Usuario ou senha incorretos";
	}
	 
}else{
	echo "Falha na autenticação, verifique a secret em bd/conexao.php";
}
?>