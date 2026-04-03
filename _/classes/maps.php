<?php
class Maps
{

    //variaveis
    private $api_key;

    //construtor
    public function __construct()
    {
        include '../bd/config.php';
        $this->api_key = KEY_GOOGLE_MAPS;
    }

    public function calcularRota($lat_ini, $lng_ini, $waypoints, $lat_fim, $lng_fim)
    {
        // Define o limite de waypoints (20, pois origem e destino já ocupam 2 posições)
        $maxWaypoints = 20;

        // Se houver mais de 20 waypoints, selecionamos amostragens espaçadas
        // Versão corrigida
        if (count($waypoints) > $maxWaypoints) {
            $count = count($waypoints);
            // Calculamos o intervalo para ter exatamente maxWaypoints pontos
            $step = ($count - 1) / ($maxWaypoints - 1);
            $filteredWaypoints = [];

            // Iteramos maxWaypoints vezes
            for ($i = 0; $i < $maxWaypoints; $i++) {
                $index = round($i * $step);
                $filteredWaypoints[] = $waypoints[$index];
            }

            $waypoints = $filteredWaypoints;
        }

        // Monta a string de waypoints corretamente
        $waypointsStr = !empty($waypoints) ? "waypoints=optimize:true|" . implode("|", $waypoints) : "";

        // Monta a URL da requisição
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$lat_ini,$lng_ini&destination=$lat_fim,$lng_fim&$waypointsStr&key=$this->api_key";

        // Faz a requisição à API do Google Maps
        $json = file_get_contents($url);
        $data = json_decode($json);

        // Verifica se a resposta é válida
        if ($data->status === "OK" && isset($data->routes[0]->legs)) {
            $total_distance = 0;
            foreach ($data->routes[0]->legs as $leg) {
                $total_distance += $leg->distance->value;
            }
            return $total_distance / 1000; // Convertendo para quilômetros
        }

        return 0; // Retorna 0 em caso de erro
    }

    public function calcularTempo($lat_ini, $lng_ini, $lat_fim, $lng_fim)
    {
        // Monta a URL da requisição
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$lat_ini,$lng_ini&destinations=$lat_fim,$lng_fim&key=$this->api_key";

        // Faz a requisição à API do Google Maps
        $json = file_get_contents($url);
        $data = json_decode($json);

        // Verifica se a resposta é válida
        if ($data->status === "OK" && isset($data->rows[0]->elements[0]->duration)) {
            // Convert seconds to minutes and round to integer
            return (int)($data->rows[0]->elements[0]->duration->value / 60); // Retorna o tempo em minutos (inteiro)
        }

        return 0; // Retorna 0 em caso de erro
    
    }

     //calcula tempo e distancia
    public function calcularTempoEDistancia($lat_ini, $lng_ini, $lat_fim, $lng_fim)
    {
        // Monta a URL da requisição
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$lat_ini,$lng_ini&destinations=$lat_fim,$lng_fim&key=$this->api_key";

        // Faz a requisição à API do Google Maps
        $json = file_get_contents($url);
        $data = json_decode($json);

        // Verifica se a resposta é válida
        if ($data->status === "OK" && isset($data->rows[0]->elements[0])) {
            $duration = (int)($data->rows[0]->elements[0]->duration->value / 60); // Tempo em minutos
            $distance = round($data->rows[0]->elements[0]->distance->value / 1000, 1); // Distância em quilômetros com uma casa decimal
            return ['tempo' => $duration, 'distancia' => $distance];
        }

        return ['tempo' => 0, 'distancia' => 0]; // Retorna 0 em caso de erro
    }
}
