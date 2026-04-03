<?php 
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/cidades.php");
include("../classes/categorias.php");
include("../classes/dinamico_mapa.php");
include("../classes/categorias_horarios.php");

$dados_retorno = array();

$cidade_id = $_POST['cidade_id'];
$endereco_ini = $_POST['endereco_ini'];
$endereco_ini = str_replace(" ", "+", $endereco_ini);

$endereco_fim = $_POST['endereco_fim'];
$endereco_fim = str_replace(" ", "+", $endereco_fim);

$categoria_id = $_POST['categoria_id'];
$lat_ini = $_POST['lat_ini'];
$lng_ini = $_POST['lng_ini'];
$lat_fim = $_POST['lat_fim'];
$lng_fim = $_POST['lng_fim'];

$c = new categorias();
$ct = new cidades();
$dm = new dinamico_mapa();
$dh = new dinamico_horarios();

$dados_categoria = $c->get_categoria($categoria_id);

$url = 'https://maps.googleapis.com/maps/api/directions/json?origin='.$lat_ini.','.$lng_ini.'&destination='.$lat_fim.','.$lng_fim.'&key='.KEY_GOOGLE_MAPS.'&language=pt-BR&region=BR&mode=driving';
$json = file_get_contents($url);
$obj = json_decode($json);

$km = $obj->routes[0]->legs[0]->distance->value;
$km = $km / 1000;

$minutos = $obj->routes[0]->legs[0]->duration->value;
$minutos = $minutos / 60;

$taxa_km = $dados_categoria[0]['tx_km'];
$taxa_km = str_replace(",", ".", $taxa_km);
$taxa_minuto = $dados_categoria[0]['tx_minuto'];
$taxa_minuto = str_replace(",", ".", $taxa_minuto);
$taxa_base = $dados_categoria[0]['tx_base'];
$taxa_base = str_replace(",", ".", $taxa_base);

//somando as taxas
$taxa = ($km * $taxa_km) + ($minutos * $taxa_minuto) + $taxa_base;

//verifica se está dentro do dinamico de horarios
$taxa_add = 0;
$taxa_add_horarios = 0;
$dinamico_horarios = $dados_categoria[0]['dinamico_horarios'];
foreach ($dinamico_horarios as $horario) {
    $taxa_h = $dh ->verifica_horario($horario);
    if($taxa_h){
        $taxa_add = str_replace(",", ".", $taxa_h['adicional']);
        if($taxa_add > $taxa_add_horarios){
            $taxa_add_horarios = $taxa_add;
            $dados_retorno['dinamico_horarios'] = $taxa_h;
        }
    }
}
//fim verifica se está dentro do dinamico de horarios

//verifica se está dentro do dinamico de mapa e pega o mais caro
$taxa_add_mapa_end_ini = 0;
$taxa_add = 0;
$dinamico_mapa = $dados_categoria[0]['dinamico_local'];
foreach ($dinamico_mapa as $din_mapa) {
    $taxa_m = $dm ->verifica_localizacao($cidade_id, $lat_ini, $lng_ini);
    if($taxa_m){
        $tx_add = str_replace(",", ".", $taxa_m['adicional']);
        if($tx_add > $taxa_add_mapa_end_ini){
            $taxa_add_mapa_end_ini = $tx_add;
            $dados_retorno['dinamico_mapa_ini'] = $taxa_m;
        }
    }
}
//fim verifica se está dentro do dinamico de mapa

$taxa_add = 0;
$taxa_add_mapa_end_fim = 0;
$dinamico_mapa = $dados_categoria[0]['dinamico_local'];
foreach ($dinamico_mapa as $din_mapa) {
    $taxa_m = $dm ->verifica_localizacao($cidade_id, $lat_fim, $lng_fim);
    if($taxa_m){
        $taxa_add = str_replace(",", ".", $taxa_m['adicional']);
        if($taxa_add > $taxa_add_mapa_end_fim){
            $taxa_add_mapa_end_fim = $taxa_add;
            $dados_retorno['dinamico_mapa_fim'] = $taxa_m;
        }
    }
}
//fim verifica se está dentro do dinamico de mapa

$taxa += $taxa_add_horarios + $taxa_add_mapa_end_ini + $taxa_add_mapa_end_fim;

//verifica taxa minima
$taxa_minima = $dados_categoria[0]['tx_minima'];
$taxa_minima = str_replace(",", ".", $taxa_minima);
if($taxa < $taxa_minima){
    $taxa = $taxa_minima;
}

$taxa = number_format($taxa, 2, ',', '.');
$minutos = number_format($minutos, 0, ',', '.');
$km = number_format($km, 2, '.', '.');

$dados_retorno['taxa'] = $taxa;
$dados_retorno['km'] = $km;
$dados_retorno['minutos'] = $minutos;

echo json_encode($dados_retorno);
?>


