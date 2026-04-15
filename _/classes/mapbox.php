<?php
Class Mapbox {
    public $mapbox_access_token;
    
    public function __construct($mapbox_access_token) {
        $this->mapbox_access_token = $mapbox_access_token;
    }

    private function haversineKm($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function getDistanciaETempo($origem_lat, $origem_lng, $destino_lat, $destino_lng) {
        $distanciaReta = $this->haversineKm($origem_lat, $origem_lng, $destino_lat, $destino_lng);
        // Evita chamadas inválidas/ruidosas na Mapbox para distâncias absurdas (ex.: GPS fora da cidade).
        if ($distanciaReta > 300) {
            return [
                'distancia' => round($distanciaReta, 1),
                'tempo' => round(($distanciaReta / 45) * 3600) // estimativa simples a 45km/h
            ];
        }

        // Ajustando o formato correto: longitude,latitude
        $origem = "$origem_lng,$origem_lat";
        $destino = "$destino_lng,$destino_lat";
    
        $url = "https://api.mapbox.com/directions/v5/mapbox/driving/$origem;$destino?access_token=$this->mapbox_access_token&geometries=geojson&overview=full";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Verifica se a requisição foi bem-sucedida
        if ($httpCode !== 200) {
            error_log("Erro na API Mapbox: HTTP $httpCode - Resposta: $response");
            return null;
        }
    
        $data = json_decode($response, true);
        
        if (isset($data['routes'][0]['distance'])) {
            $distanciaKm = $data['routes'][0]['distance'] / 1000; // Convertendo metros para km
            $tempoSegundos = $data['routes'][0]['duration'];
           

            
            return [
                'distancia' => round($distanciaKm, 1), // Arredondando para 2 casas decimais
                'tempo' => $tempoSegundos
            ];
        }
    
        return null;
    }

    public function geocoding($endereco) {
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($endereco) . ".json?access_token=$this->mapbox_access_token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida
        if ($httpCode !== 200) {
            error_log("Erro na API Mapbox: HTTP $httpCode - Resposta: $response");
            return null;
        }

        return json_decode($response, true);
    
    }

    public function getEndereco($lat, $lng) {
        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/$lng,$lat.json?access_token=$this->mapbox_access_token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Verifica se a requisição foi bem-sucedida
        if ($httpCode !== 200) {
            error_log("Erro na API Mapbox: HTTP $httpCode - Resposta: $response");
        }

        return json_decode($response, true);
    }

    public function getDistanciaETempoEndereco($endereco_origem, $endereco_destino) {
        // Função auxiliar para obter coordenadas a partir do endereço
        function getCoordenadas($endereco, $access_token) {
            $endereco = urlencode($endereco);
            $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/$endereco.json?access_token=$access_token";
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
    
            $data = json_decode($response, true);
    
            if (isset($data['features'][0]['geometry']['coordinates'])) {
                return $data['features'][0]['geometry']['coordinates']; // [longitude, latitude]
            }
    
            return null;
        }
    
        // Obter coordenadas da origem e destino
        $origem_coords = getCoordenadas($endereco_origem, $this->mapbox_access_token);
        $destino_coords = getCoordenadas($endereco_destino, $this->mapbox_access_token);
    
        if (!$origem_coords || !$destino_coords) {
            error_log("Erro ao obter coordenadas para os endereços informados.");
            return null;
        }
    
        // Criar as strings de coordenadas no formato correto (longitude,latitude)
        $origem = implode(',', $origem_coords);
        $destino = implode(',', $destino_coords);
    
        // Construir a URL da requisição de rota
        $url = "https://api.mapbox.com/directions/v5/mapbox/driving/$origem;$destino?access_token=$this->mapbox_access_token&geometries=geojson&overview=full";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Verifica se a requisição foi bem-sucedida
        if ($httpCode !== 200) {
            error_log("Erro na API Mapbox: HTTP $httpCode - Resposta: $response");
            return null;
        }
    
        $data = json_decode($response, true);
    
        if (isset($data['routes'][0]['distance'])) {
            $distanciaKm = $data['routes'][0]['distance'] / 1000; // Convertendo metros para km
            $tempoSegundos = $data['routes'][0]['duration'];
    
            return [
                'distancia' => round($distanciaKm, 2), // Arredondando para 2 casas decimais
                'tempo' => $tempoSegundos,
                'lat_ini' => $origem_coords[1], // Latitude da origem
                'lng_ini' => $origem_coords[0], // Longitude da origem
                'lat_fim' => $destino_coords[1], // Latitude do destino
                'lng_fim' => $destino_coords[0] // Longitude do destino
            ];
        }
    
        return null;
    }
    
}
