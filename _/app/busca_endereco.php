<?php
/**
 * Autocomplete de endereços para o app.
 * Prioridade: Google Places (location bias) → Mapbox Geocoding (proximity + bbox).
 * GET/POST: q (mín. 3 caracteres), lat, lng e radius opcionais (metros, padrão 50000).
 * Retorno: JSON array [{ place_id, name, display_name, lat, lon }, ...]
 */
header('access-control-allow-origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once __DIR__ . '/../bd/config.php';

$q = '';
if (isset($_GET['q'])) {
    $q = trim((string) $_GET['q']);
} elseif (isset($_POST['q'])) {
    $q = trim((string) $_POST['q']);
}

if (strlen($q) < 3) {
    echo json_encode([]);
    exit;
}

$lat = 0.0;
$lng = 0.0;
if (isset($_GET['lat'])) {
    $lat = floatval($_GET['lat']);
} elseif (isset($_POST['lat'])) {
    $lat = floatval($_POST['lat']);
}
if (isset($_GET['lng'])) {
    $lng = floatval($_GET['lng']);
} elseif (isset($_POST['lng'])) {
    $lng = floatval($_POST['lng']);
}

$radius = 50000;
if (isset($_GET['radius'])) {
    $radius = max(1000, min(100000, intval($_GET['radius'])));
} elseif (isset($_POST['radius'])) {
    $radius = max(1000, min(100000, intval($_POST['radius'])));
}

$hasCoords = is_finite($lat) && is_finite($lng) && ($lat != 0.0 || $lng != 0.0);

function google_place_details($placeId, $apiKey)
{
    $url = 'https://maps.googleapis.com/maps/api/place/details/json'
        . '?place_id=' . rawurlencode($placeId)
        . '&fields=geometry,formatted_address,name'
        . '&language=pt-BR'
        . '&key=' . rawurlencode($apiKey);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !is_string($response) || $response === '') {
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data) || ($data['status'] ?? '') !== 'OK' || !isset($data['result']['geometry']['location'])) {
        return null;
    }

    $loc = $data['result']['geometry']['location'];
    return [
        'place_id' => (string) $placeId,
        'name' => isset($data['result']['name']) ? (string) $data['result']['name'] : '',
        'display_name' => isset($data['result']['formatted_address']) ? (string) $data['result']['formatted_address'] : '',
        'lat' => (string) $loc['lat'],
        'lon' => (string) $loc['lng'],
    ];
}

function search_google_places($q, $lat, $lng, $radius, $apiKey)
{
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json'
        . '?input=' . rawurlencode($q)
        . '&components=country:br'
        . '&language=pt-BR'
        . '&key=' . rawurlencode($apiKey);

    if (is_finite($lat) && is_finite($lng)) {
        $url .= '&location=' . rawurlencode($lat . ',' . $lng)
            . '&radius=' . rawurlencode((string) $radius)
            . '&strictbounds=false';
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    $response = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !is_string($response) || $response === '') {
        return [];
    }

    $data = json_decode($response, true);
    if (!is_array($data) || ($data['status'] ?? '') !== 'OK' || !isset($data['predictions']) || !is_array($data['predictions'])) {
        return [];
    }

    $out = [];
    $predictions = array_slice($data['predictions'], 0, 8);
    foreach ($predictions as $prediction) {
        if (!isset($prediction['place_id'])) {
            continue;
        }
        $details = google_place_details($prediction['place_id'], $apiKey);
        if ($details !== null) {
            $out[] = $details;
        }
    }

    return $out;
}

function search_mapbox_places($q, $lat, $lng, $radius)
{
    if (!defined('MAPBOX_KEY') || MAPBOX_KEY === '') {
        return [];
    }

    $encoded = rawurlencode($q);
    $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $encoded . '.json'
        . '?access_token=' . rawurlencode(MAPBOX_KEY)
        . '&country=BR&limit=10&language=pt&types=address,poi,place,locality,neighborhood';

    if (is_finite($lat) && is_finite($lng)) {
        $url .= '&proximity=' . rawurlencode($lng . ',' . $lat);
        $delta = max(0.05, min(1.0, ($radius / 1000) / 111));
        $minLon = $lng - $delta;
        $minLat = $lat - $delta;
        $maxLon = $lng + $delta;
        $maxLat = $lat + $delta;
        $url .= '&bbox=' . rawurlencode("$minLon,$minLat,$maxLon,$maxLat");
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    $response = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !is_string($response) || $response === '') {
        return [];
    }

    $data = json_decode($response, true);
    if (!is_array($data) || !isset($data['features']) || !is_array($data['features'])) {
        return [];
    }

    $out = [];
    foreach ($data['features'] as $f) {
        if (!isset($f['center'][0], $f['center'][1])) {
            continue;
        }
        $out[] = [
            'place_id' => isset($f['id']) ? (string) $f['id'] : uniqid('mb_', true),
            'name' => isset($f['text']) ? (string) $f['text'] : '',
            'display_name' => isset($f['place_name']) ? (string) $f['place_name'] : '',
            'lat' => (string) $f['center'][1],
            'lon' => (string) $f['center'][0],
        ];
    }

    return $out;
}

$out = [];

if (defined('KEY_GOOGLE_MAPS') && KEY_GOOGLE_MAPS !== '') {
    $out = search_google_places($q, $lat, $lng, $radius, KEY_GOOGLE_MAPS);
}

if (count($out) === 0) {
    $out = search_mapbox_places($q, $lat, $lng, $radius);
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
