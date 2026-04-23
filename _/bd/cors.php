<?php
/**
 * CORS Handler centralizado - Inclua este arquivo no início de qualquer endpoint.
 * Libera CORS para todas as origens, métodos e headers.
 */

// Permite qualquer origem
header('Access-Control-Allow-Origin: *');

// Métodos permitidos
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');

// Headers permitidos (incluindo os que o Axios pode enviar)
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');

// Cache do preflight por 1 hora
header('Access-Control-Max-Age: 3600');

// Se for uma requisição OPTIONS (preflight), responde com 200 e encerra
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
