<?php
// Desativa exibição de erros em produção (opcional)
// error_reporting(0);
// ini_set("display_errors", 0);

$hostname = "localhost";
$user = "uberzapapp_transporte";
$password = "59qM?6obK^89XSD;";
$database = "uberzapapp_transporte";

// Conexão MySQLi
$conexao = mysqli_connect($hostname, $user, $password, $database);

// Verifica se a conexão MySQLi foi bem-sucedida
if (!$conexao) {
    die("Falha na Conexão com o Banco de Dados (MySQLi): " . mysqli_connect_error());
}

// Define charset e timezone no MySQLi
mysqli_set_charset($conexao, "utf8mb4");
@mysqli_query($conexao, "SET time_zone = '-04:00';");

// Conexão PDO com tratamento de erro
try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    @$pdo->exec("SET time_zone = '-04:00';");
} catch (PDOException $e) {
    die("Erro na conexão PDO: " . $e->getMessage());
}

// Configura fuso horário do PHP para Cuiabá
date_default_timezone_set('America/Cuiaba');

// Chave secreta usada no sistema
$secret = "abc1234";
?>