<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/cidades.php");
include("../classes/categorias.php");
include("../classes/dinamico_mapa.php");
include("../classes/categorias_horarios.php");
include("../classes/motoristas.php");
include("../classes/funcoes.php");
include("../classes/mapbox.php");

$dados_retorno = array();

$cidade_id = $_POST['cidade_id'];

$lat_ini = $_POST['lat_ini'];
$lng_ini = $_POST['lng_ini'];
$lat_fim = $_POST['lat_fim'];
$lng_fim = $_POST['lng_fim'];

$c = new categorias();
$ct = new cidades();
$dm = new dinamico_mapa();
$dh = new dinamico_horarios();
$mot = new motoristas();
$func = new Funcoes();
$mapbox = new Mapbox(MAPBOX_KEY);

$categorias = $c->get_categorias($cidade_id);

$url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $lat_ini . ',' . $lng_ini . '&destination=' . $lat_fim . ',' . $lng_fim . '&key=' . KEY_GOOGLE_MAPS . '&language=pt-BR&region=BR&mode=driving';
$json = file_get_contents($url);
$obj = json_decode($json);

$km = $obj->routes[0]->legs[0]->distance->value;
$km = $km / 1000;

$minutos = $obj->routes[0]->legs[0]->duration->value;
$minutos = $minutos / 60;
$retorno_fim = array();

$motoristas_disponiveis = $mot->get_motoristas($cidade_id, true);

foreach ($categorias as $dados_categoria) {

    $taxa_km = $dados_categoria['tx_km'];
    $taxa_km = str_replace(",", ".", $taxa_km);
    $taxa_minuto = $dados_categoria['tx_minuto'];
    $taxa_minuto = str_replace(",", ".", $taxa_minuto);
    $taxa_base = $dados_categoria['tx_base'];
    $taxa_base = str_replace(",", ".", $taxa_base);

    //somando as taxas
    $taxa = ($km * $taxa_km) + ($minutos * $taxa_minuto) + $taxa_base;

    //verifica se está dentro do dinamico de horarios
    $taxa_add = 0;
    $taxa_add_horarios = 0;
    $dinamico_horarios = $dados_categoria['dinamico_horarios'];
    foreach ($dinamico_horarios as $horario) {
        $taxa_h = $dh->verifica_horario($horario);
        if ($taxa_h) {
            $taxa_add = str_replace(",", ".", $taxa_h['adicional']);
            if ($taxa_add > $taxa_add_horarios) {
                $taxa_add_horarios = $taxa_add;
                $dados_retorno['dinamico_horarios'] = $taxa_h;
            }
        }
    }
    //fim verifica se está dentro do dinamico de horarios

    //verifica se está dentro do dinamico de mapa e pega o mais caro
    $taxa_add_mapa_end_ini = 0;
    $taxa_add = 0;
    $dinamico_mapa = $dados_categoria['dinamico_local'];
    foreach ($dinamico_mapa as $din_mapa) {
        $taxa_m = $dm->verifica_localizacao($cidade_id, $lat_ini, $lng_ini);
        if ($taxa_m) {
            $tx_add = str_replace(",", ".", $taxa_m['adicional']);
            if ($tx_add > $taxa_add_mapa_end_ini) {
                $taxa_add_mapa_end_ini = $tx_add;
                $dados_retorno['dinamico_mapa_ini'] = $taxa_m;
            }
        }
    }
    //fim verifica se está dentro do dinamico de mapa

    $taxa_add = 0;
    $taxa_add_mapa_end_fim = 0;
    $dinamico_mapa = $dados_categoria['dinamico_local'];
    foreach ($dinamico_mapa as $din_mapa) {
        $taxa_m = $dm->verifica_localizacao($cidade_id, $lat_fim, $lng_fim);
        if ($taxa_m) {
            $taxa_add = str_replace(",", ".", $taxa_m['adicional']);
            if ($taxa_add > $taxa_add_mapa_end_fim) {
                $taxa_add_mapa_end_fim = $taxa_add;
                $dados_retorno['dinamico_mapa_fim'] = $taxa_m;
            }
        }
    }
    //fim verifica se está dentro do dinamico de mapa

    $taxa += $taxa_add_horarios + $taxa_add_mapa_end_ini + $taxa_add_mapa_end_fim;

    //verifica taxa minima
    $taxa_minima = $dados_categoria['tx_minima'];
    $taxa_minima = str_replace(",", ".", $taxa_minima);
    if ($taxa < $taxa_minima) {
        $taxa = $taxa_minima;
    }

    $taxa = number_format($taxa, 2, ',', '.');
    $minutos = number_format($minutos, 0, ',', '.');
    $km = number_format($km, 2, '.', '.');

    //aqui vamos incluir os motoristas que estão disponiveis para essa categoria
    $motoristas_disponiveis_categoria = array();
    if (!empty($motoristas_disponiveis)) {

        foreach ($motoristas_disponiveis as $motorista) {
            $ids_categorias_motorista = json_decode($motorista['ids_categorias'], true);
            if (!is_array($ids_categorias_motorista)) {
                $ids_categorias_motorista = [$ids_categorias_motorista];
            }
            if (in_array($dados_categoria['id'], $ids_categorias_motorista)) {
                $motoristas_disponiveis_categoria[] = $motorista;
            }
        }
        //agora vamos ver qual motorista está mais perto da origem
        $motoristas_ordenados = array();
        foreach ($motoristas_disponiveis_categoria as $motorista) {
            $distancia = $func->obterDistanciaPorCoordenadas($lat_ini, $lng_ini, $motorista['latitude'], $motorista['longitude']);
            $motoristas_ordenados[$motorista['id']] = $distancia;
        }
        asort($motoristas_ordenados);
        $motoristas_ordenados = array_keys($motoristas_ordenados);
        //agora pegamos o motorista mais proximo e verificamos usando a classe mapbox o tempo e distancia dele até o ponto de embarque
        if (!empty($motoristas_ordenados)) {
            $motorista_mais_proximo_id = $motoristas_ordenados[0];
            $motorista_mais_proximo = $mot->get_motorista($motorista_mais_proximo_id);
            if (isset($motorista_mais_proximo['latitude']) && isset($motorista_mais_proximo['longitude'])) {
                $tempoDistancia = $mapbox->getDistanciaETempo($motorista_mais_proximo['latitude'], $motorista_mais_proximo['longitude'], $lat_ini, $lng_ini);
            } else {
                $tempoDistancia = [
                    'tempo' => 0,
                    'distancia' => 0
                ];
            }
        } else {
            $tempoDistancia = [
                'tempo' => 0,
                'distancia' => 0
            ];
        }
        if ($tempoDistancia) {
            $motorista_mais_proximo['tempo'] = $tempoDistancia['tempo'];
            $motorista_mais_proximo['distancia'] = $tempoDistancia['distancia'];
        }
    } else {
        $tempoDistancia = [
            'tempo' => 0,
            'distancia' => 0
        ];
    }

    $dados_retorno['id'] = $dados_categoria['id'];
    $dados_retorno['nome'] = $dados_categoria['nome'];
    $dados_retorno['descricao'] = $dados_categoria['descricao'];
    $dados_retorno['img'] = $dados_categoria['img'];
    $dados_retorno['taxa'] = $taxa;
    $dados_retorno['ordem'] = $dados_categoria['ordem'];
    if ($tempoDistancia && isset($tempoDistancia['distancia']) && isset($tempoDistancia['tempo'])) {
        $dados_retorno['motorista_km'] = $tempoDistancia['distancia'];
        $dados_retorno['motorista_tempo'] = round($tempoDistancia['tempo'] / 60);
    } else {
        $dados_retorno['motorista_km'] = 0;
        $dados_retorno['motorista_tempo'] = 0;
    }
    //$dados_retorno['motoristas_disponiveis'] = $motoristas_disponiveis;
    $retorno_fim['categorias'][] = $dados_retorno;
}
//ordenando array de categorias pelo campo ordem
$ordem = array();
foreach ($retorno_fim['categorias'] as $key => $row) {
    $ordem[$key] = $row['ordem'];
}
array_multisort($ordem, SORT_ASC, $retorno_fim['categorias']);
//fim ordenando array de categorias pelo campo ordem
$retorno_fim['dados']['km'] = $km;
$retorno_fim['dados']['minutos'] = $minutos;
echo json_encode($retorno_fim);
