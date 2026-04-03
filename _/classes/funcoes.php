<?php
class Funcoes
{
    //obtem a distancia usando o calculo de haversine
    public function obterDistanciaPorCoordenadas($lat_ini, $lng_ini, $lat_fim, $lng_fim)
    {
        $earthRadius = 6371; // Raio da Terra em km
        $dLat = deg2rad($lat_fim - $lat_ini);
        $dLng = deg2rad($lng_fim - $lng_ini);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat_ini)) * cos(deg2rad($lat_fim)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c * 1000; // Converte de km para metros
    }
}
