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
	$cidade_id = $_POST['cidade_id'];
	$id_motorista = $_POST['id_motorista'];
	
	// Busca corridas disponíveis EXCLUINDO as que o motorista já recusou pessoalmente.
    // Em bases antigas, a tabela corridas_rejeitadas pode ainda não existir.
    // Nesse caso, faz fallback para a query clássica sem recusa individual.
    try {
        $sql_dispo = "SELECT c.* FROM corridas c 
                      LEFT JOIN corridas_rejeitadas cr ON (c.id = cr.id_corrida AND cr.id_motorista = :id_moto)
                      WHERE c.cidade_id = :cid AND c.status = '0' AND cr.id_corrida IS NULL 
                      ORDER BY c.date ASC";
        $stmt_dispo = $pdo->prepare($sql_dispo);
        $stmt_dispo->execute(['id_moto' => $id_motorista, 'cid' => $cidade_id]);
        $corridas = $stmt_dispo->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 42S02 = tabela/visão inexistente.
        if ($e->getCode() === '42S02') {
            $sql_dispo = "SELECT * FROM corridas WHERE cidade_id = :cid AND status = '0' ORDER BY date ASC";
            $stmt_dispo = $pdo->prepare($sql_dispo);
            $stmt_dispo->execute(['cid' => $cidade_id]);
            $corridas = $stmt_dispo->fetchAll(PDO::FETCH_ASSOC);
        } else {
            throw $e;
        }
    }

    $dados_motorista = $m->get_motorista($id_motorista);
    
    // Injeção de variáveis faltantes:
    $ids_categorias = json_decode($dados_motorista['ids_categorias'], true);
    if (!is_array($ids_categorias)) $ids_categorias = array();
    
    $latitude_motorista = $dados_motorista['latitude'];
    $longitude_motorista = $dados_motorista['longitude'];
    
    // Fallback de GPS: Se as coordenadas estiverem zeradas (0,0), assume Avenida Paulista
    if (!$latitude_motorista || $latitude_motorista == 0) $latitude_motorista = -23.561706;
    if (!$longitude_motorista || $longitude_motorista == 0) $longitude_motorista = -46.655981;
	
	if ($corridas) {
		foreach ($corridas as $key => $value) {
            // Filtro de tempo removido para evitar problemas de fuso horário
			
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
