<?php
/**
 * Conexão central MySQL (MySQLi + PDO).
 * Evita PDO persistente — causa comum de "Packets out of order" em hospedagem compartilhada/VPS.
 */
include_once __DIR__ . '/cors.php';

global $conexao, $pdo;

$hostname = '69.62.99.122';
$user = 'uberzapbd';
$password = 'uberzapbd';
$database = 'uberzapbd';
$port = 1212;

date_default_timezone_set('America/Cuiaba');

$secret = 'abc1234';

if (!function_exists('ubezap_create_mysqli')) {
function ubezap_create_mysqli()
{
    global $hostname, $user, $password, $database, $port;

    $link = mysqli_init();
    if (!$link) {
        throw new RuntimeException('Falha ao inicializar MySQLi.');
    }

    mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

    if (!@mysqli_real_connect($link, $hostname, $user, $password, $database, $port)) {
        throw new RuntimeException('Falha na conexão MySQLi: ' . mysqli_connect_error());
    }

    mysqli_set_charset($link, 'utf8mb4');
    @mysqli_query($link, "SET time_zone = '-04:00'");

    return $link;
}

function ubezap_mysqli_is_alive($link)
{
    if (!$link instanceof mysqli) {
        return false;
    }
    return @mysqli_ping($link);
}

function ubezap_get_mysqli()
{
    global $conexao;

    if (!ubezap_mysqli_is_alive($conexao)) {
        if ($conexao instanceof mysqli) {
            @mysqli_close($conexao);
        }
        $conexao = ubezap_create_mysqli();
    }

    return $conexao;
}

function ubezap_create_pdo()
{
    global $hostname, $user, $password, $database, $port;

    $dsn = "mysql:host={$hostname};port={$port};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_TIMEOUT => 10,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '-04:00'",
    ]);

    return $pdo;
}

function ubezap_pdo_is_alive($pdo)
{
    if (!$pdo instanceof PDO) {
        return false;
    }
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function ubezap_get_pdo()
{
    global $pdo;

    if (!ubezap_pdo_is_alive($pdo)) {
        $pdo = null;
        $pdo = ubezap_create_pdo();
    }

    return $pdo;
}
}

try {
    $conexao = ubezap_get_mysqli();
    $pdo = ubezap_get_pdo();
} catch (Throwable $e) {
    if (php_sapi_name() !== 'cli') {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(503);
        }
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha temporária na conexão com o banco de dados.',
        ], JSON_UNESCAPED_UNICODE);
    } else {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
    }
    exit;
}

?>
