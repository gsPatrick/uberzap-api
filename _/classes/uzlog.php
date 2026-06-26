<?php
/**
 * Log simples para acompanhar a API no terminal da VPS.
 * Escreve em _/logs/ubezap.log (tail -f) e também no error_log (stderr -> logs
 * do container no EasyPanel). Veja os últimos via: _/logs.php?secret=...
 */
if (!function_exists('uzlog')) {
    function uzlog($msg, $context = null)
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
        if ($context !== null) {
            $line .= ' ' . (is_string($context) ? $context : json_encode($context, JSON_UNESCAPED_UNICODE));
        }
        $dir = __DIR__ . '/../logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($dir . '/ubezap.log', $line . "\n", FILE_APPEND | LOCK_EX);
        error_log('[ubezap] ' . $line);
    }
}
