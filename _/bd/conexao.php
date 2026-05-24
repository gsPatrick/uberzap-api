<?php
/**
 * Conexão central MySQL (PDO principal + MySQLi opcional/legacy).
 */
include_once __DIR__ . '/cors.php';

global $conexao, $pdo;

// Easypanel / Docker: defina DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME no painel
$hostname = getenv('DB_HOST') ?: (getenv('MYSQL_HOST') ?: '69.62.99.122');
$user = getenv('DB_USER') ?: (getenv('MYSQL_USER') ?: 'uberzapbd');
$password = getenv('DB_PASSWORD') ?: (getenv('MYSQL_PASSWORD') ?: 'uberzapbd');
$database = getenv('DB_NAME') ?: (getenv('MYSQL_DATABASE') ?: 'uberzapbd');
$port = (int) (getenv('DB_PORT') ?: (getenv('MYSQL_PORT') ?: 1212));

if (file_exists(__DIR__ . '/config.db.php')) {
    include __DIR__ . '/config.db.php';
}

date_default_timezone_set('America/Cuiaba');
$secret = getenv('APP_SECRET') ?: 'abc1234';

if (!function_exists('ubezap_db_fail')) {
    function ubezap_db_fail($message, Throwable $e = null)
    {
        if ($e) {
            error_log('[ubezap/conexao.php] ' . $message . ' — ' . $e->getMessage());
        } else {
            error_log('[ubezap/conexao.php] ' . $message);
        }

        if (php_sapi_name() === 'cli') {
            if ($e) {
                fwrite(STDERR, $e->getMessage() . PHP_EOL);
            }
            exit(1);
        }

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(503);
        }

        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha temporária na conexão com o banco de dados.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('ubezap_create_pdo')) {
    function ubezap_pdo_dsn_attempts()
    {
        global $hostname, $port;

        $seen = [];
        $attempts = [];

        $add = function ($host, $p) use (&$seen, &$attempts) {
            $key = $host . ':' . $p;
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $attempts[] = ['host' => $host, 'port' => $p];
        };

        $add($hostname, $port);
        if ($port !== 3306) {
            $add($hostname, 3306);
        }

        // PHP em Docker na mesma VM: IP público costuma falhar (hairpin NAT)
        foreach (['127.0.0.1', 'host.docker.internal'] as $localHost) {
            if ($localHost !== $hostname) {
                $add($localHost, $port);
                if ($port !== 3306) {
                    $add($localHost, 3306);
                }
            }
        }

        return $attempts;
    }

    function ubezap_create_pdo()
    {
        global $user, $password, $database;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        if (defined('PDO::MYSQL_ATTR_CONNECT_TIMEOUT')) {
            $options[PDO::MYSQL_ATTR_CONNECT_TIMEOUT] = 10;
        }

        $lastError = null;

        foreach (ubezap_pdo_dsn_attempts() as $attempt) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $attempt['host'],
                    $attempt['port'],
                    $database
                );
                $pdo = new PDO($dsn, $user, $password, $options);
                @$pdo->exec("SET time_zone = '-04:00'");
                return $pdo;
            } catch (PDOException $e) {
                $lastError = $e;
                error_log(sprintf(
                    '[ubezap/conexao.php] PDO tentativa %s:%d falhou: %s',
                    $attempt['host'],
                    $attempt['port'],
                    $e->getMessage()
                ));
            }
        }

        throw $lastError ?: new RuntimeException('Não foi possível conectar ao MySQL via PDO.');
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
            $pdo = ubezap_create_pdo();
        }

        return $pdo;
    }

    function ubezap_create_mysqli()
    {
        global $hostname, $user, $password, $database, $port;

        $link = @mysqli_connect($hostname, $user, $password, $database, $port);
        if (!$link) {
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
}

// Bootstrap uma única vez por requisição — login/API usam PDO
if (!defined('UBEZAP_DB_BOOTSTRAPPED')) {
    define('UBEZAP_DB_BOOTSTRAPPED', true);

    try {
        $pdo = ubezap_get_pdo();
    } catch (Throwable $e) {
        ubezap_db_fail('Falha ao conectar PDO', $e);
    }

    // MySQLi é opcional (scripts legados). Não derruba API se falhar.
    $conexao = null;
    try {
        $conexao = ubezap_get_mysqli();
    } catch (Throwable $e) {
        error_log('[ubezap/conexao.php] MySQLi indisponível (PDO ok): ' . $e->getMessage());
        $conexao = null;
    }
}

?>
