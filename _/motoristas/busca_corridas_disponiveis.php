<?php
include("../bd/config.php");
include("../classes/motoristas.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/clientes.php");
include("../classes/tempo.php");
include("../classes/categorias.php");
include("../classes/mapbox.php");

$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
	$c = new corridas();
	$t = new tempo();
	$clientes = new clientes();
	$cat = new categorias();
	$m = new motoristas();
	$mapbox = new Mapbox(MAPBOX_KEY);
	$cidade_id = $_POST['cidade_id'];
	$id_motorista = $_POST['id_motorista'];
	$corridas  = $c->get_corridas_disponiveis($cidade_id);
	$dados_motorista = $m->get_motorista($id_motorista);
	$latitude_motorista = $dados_motorista['latitude'];
	$longitude_motorista = $dados_motorista['longitude'];
	//se motorista online = 2 retorna no
	if ($dados_motorista['online'] == 2) {
		echo "no";
		exit;
	} 
	$ids_categorias = $dados_motorista['ids_categorias'];
	$ids_categorias = json_decode($ids_categorias);
	if ($corridas) {
		foreach ($corridas as $key => $value) {
			$id_categoria = $value['categoria_id'];
			if (!in_array($id_categoria, $ids_categorias)) {
				unset($corridas[$key]);
			} else {
				$latitude_embarque = $value['lat_ini'];
				$longitude_embarque = $value['lng_ini'];
				$tempoeKm = $mapbox->getDistanciaETempo($latitude_motorista, $longitude_motorista, $latitude_embarque, $longitude_embarque);
				if(!$tempoeKm){
					$tempoeKm = ['distancia' => 0, 'tempo' => 0];
					$corridas[$key]['resposta_mapbox'] = 'erro';
				}
				$corridas[$key]['tempo_embarque'] = round($tempoeKm['tempo'] / 60, 1);
				$corridas[$key]['distancia_embarque'] = $tempoeKm['distancia'];
				if ($value['cliente_id'] != 0) {
					$cliente = $clientes->get_cliente_id($value['cliente_id']);
					$corridas[$key]['telefone_cliente'] = $cliente['telefone'];
				} else {
					$corridas[$key]['telefone_cliente'] = "Indisponível";
				}
				$corridas[$key]['hora'] = $t->data_mysql_para_user($value['date']) . " às " . $t->hora_mysql_para_user($value['date']);
				$corridas[$key]['categoria'] = $cat->get_categoria($value['categoria_id'])[0]['nome'];
			}
		}
		//reorganiza indices
		$corridas = array_values($corridas);
		if (count($corridas) == 0) {
			echo "no";
			exit;
		}
		echo json_encode($corridas);

		//var_dump($ids_categorias);
	} else {
		echo "no";
	}
}
