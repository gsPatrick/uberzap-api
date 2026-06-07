<?php
/**
 * Conexão central MySQL (PDO principal + MySQLi opcional/legacy).
 */
include_once __DIR__ . '/cors.php';

global $conexao, $pdo;

if (!function_exists('ubezap_env')) {
    function ubezap_env($keys, $default)
    {
        foreach ((array) $keys as $key) {
            $value = getenv($key);
            if ($value !== false && $value !== '') {
                return $value;
            }
        }
        return $default;
    }
}

if (!function_exists('ubezap_normalize_db_config')) {
    function ubezap_normalize_db_config()
    {
        global $hostname, $user, $password, $database, $port;

        $hostname = trim((string) $hostname);
        if ($hostname === '') {
            $hostname = '195.250.26.221';
        }

        $port = (int) $port;
        if ($port < 1 || $port > 65535) {
            $port = 3306;
        }

        if (!is_string($user) || trim($user) === '') {
            $user = 'uberzapapp_transporte';
        }
        if (!is_string($password) || $password === '') {
            $password = '59qM?6obK^89XSD;';
        }
        if (!is_string($database) || trim($database) === '') {
            $database = 'uberzapapp_transporte';
        }
    }
}

// =========================================================================
//  PRODUÇÃO HARDCODED — credenciais fixas, IGNORANDO variáveis de ambiente.
//  Motivo: o env do painel apontava para homologação e, por ter prioridade,
//  sobrescrevia estes valores (o app conectava no banco errado/morto).
//  Para voltar a usar env/config.db.php, restaure o bloco ubezap_env() acima.
// =========================================================================
$hostname = '195.250.26.221';
$user     = 'uberzapapp_transporte';
$password = '59qM?6obK^89XSD;';
$database = 'uberzapapp_transporte';
$port     = 3306;

ubezap_normalize_db_config();

date_default_timezone_set('America/Cuiaba');
$secret = 'abc1234';

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
            $host = trim((string) $host);
            $p = (int) $p;
            if ($host === '' || $p < 1 || $p > 65535) {
                return;
            }
            $key = $host . ':' . $p;
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $attempts[] = ['host' => $host, 'port' => $p];
        };

        ubezap_normalize_db_config();
        $add($hostname, $port);

        if ($port !== 3306) {
            $add($hostname, 3306);
        }

        // Fallback local só se o host principal não for localhost
        if (!in_array($hostname, ['127.0.0.1', 'localhost'], true)) {
            $add('127.0.0.1', $port);
            if ($port !== 3306) {
                $add('127.0.0.1', 3306);
            }
        }

        return $attempts;
    }

    function ubezap_create_pdo()
    {
        global $user, $password, $database;

        ubezap_normalize_db_config();

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
