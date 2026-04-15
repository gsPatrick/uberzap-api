<?php
/**
 * Autocomplete de endereços para o app (Mapbox Geocoding — mesmo stack de calcular_custos.php).
 * GET/POST: q (mín. 3 caracteres), lat e lng opcionais (proximity, longitude primeiro).
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

if (!defined('MAPBOX_KEY') || MAPBOX_KEY === '') {
    echo json_encode([]);
    exit;
}

$encoded = rawurlencode($q);
$url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $encoded . '.json'
    . '?access_token=' . rawurlencode(MAPBOX_KEY)
    . '&country=BR&limit=10&language=pt&types=address,poi,place,locality,neighborhood';

if ($lat != 0.0 || $lng != 0.0) {
    if (is_finite($lat) && is_finite($lng)) {
        $url .= '&proximity=' . rawurlencode($lng . ',' . $lat);
    }
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);
$response = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200 || !is_string($response) || $response === '') {
    echo json_encode([]);
    exit;
}

$data = json_decode($response, true);
if (!is_array($data) || !isset($data['features']) || !is_array($data['features'])) {
    echo json_encode([]);
    exit;
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

echo json_encode($out, JSON_UNESCAPED_UNICODE);
