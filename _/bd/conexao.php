<?php
// CORS - Libera para todas as origens
include_once __DIR__ . '/cors.php';

global $conexao, $pdo;

$hostname = "69.62.99.122";
$user = "uberzapbd";
$password = "uberzapbd";
$database = "uberzapbd";
$port = 1212;

// Conexão MySQLi (reaproveita se já existir)
if (!isset($conexao) || !$conexao || !($conexao instanceof mysqli) || @!mysqli_ping($conexao)) {
    $conexao = @mysqli_connect($hostname, $user, $password, $database, $port);
    if (!$conexao) {
        die("Falha na Conexão com o Banco de Dados (MySQLi): " . mysqli_connect_error());
    }
    mysqli_set_charset($conexao, "utf8mb4");
    @mysqli_query($conexao, "SET time_zone = '-04:00';");
}

// Conexão PDO (reaproveita se já existir com conexão persistente)
if (!isset($pdo) || !$pdo || !($pdo instanceof PDO)) {
    try {
        $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database;charset=utf8mb4", $user, $password, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        @$pdo->exec("SET time_zone = '-04:00';");
    } catch (PDOException $e) {
        die("Erro na conexão PDO: " . $e->getMessage());
    }
}

// Configura fuso horário do PHP para Cuiabá
date_default_timezone_set('America/Cuiaba');

// Chave secreta usada no sistema
$secret = "abc1234";
?>