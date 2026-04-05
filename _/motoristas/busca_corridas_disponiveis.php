<?php
include("../bd/config.php");
include("../classes/motoristas.php");
include("../classes/corridas.php");
include("../classes/seguranca.php");
include("../classes/clientes.php");
include("../classes/tempo.php");
include("../classes/categorias.php");
include("../classes/mapbox.php");
include("../bd/conexao.php"); // Adicionado para suportar a nova query de recusas

$secret_key = $_POST['secret'];

$s = new seguranca();
if ($s->compare_secret($secret_key)) {
	$c = new corridas();
	$t = new tempo();
	$clientes = new clientes();
	$cat = new categorias();
	$m = new motoristas();
	$mapbox = new Mapbox(MAPBOX_KEY);
	$id_motorista = $_POST['id_motorista'];
	
	// Busca corridas disponíveis que NÃO foram rejeitadas por este motorista
	$sql_disponiveis = "SELECT * FROM corridas 
                        WHERE cidade_id = :cidade_id 
                        AND status = '0' 
                        AND id NOT IN (SELECT id_corrida FROM corridas_rejeitadas WHERE id_motorista = :id_motorista)
                        ORDER BY date ASC";
	$stmt_disp = $pdo->prepare($sql_disponiveis);
	$stmt_disp->execute(['cidade_id' => $cidade_id, 'id_motorista' => $id_motorista]);
	$corridas = $stmt_disp->fetchAll(PDO::FETCH_ASSOC);
	
	$dados_motorista = $m->get_motorista($id_motorista);
	
	// Filtro de tempo: Somente corridas dos últimos 15 minutos
	$agora = time();
	$limite_tempo = 15 * 60; // 15 minutos em segundos

	if ($corridas) {
		foreach ($corridas as $key => $value) {
			$data_corrida = strtotime($value['date']);
			if (($agora - $data_corrida) > $limite_tempo) {
				unset($corridas[$key]);
				continue;
			}
			
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
