<?php
/**
 * Helpers de normalização — CPF/telefone com ou sem máscara.
 */

if (!function_exists('ubezap_digits_only')) {
    function ubezap_digits_only($value)
    {
        return preg_replace('/\D/', '', (string) $value);
    }
}

if (!function_exists('ubezap_post_digits')) {
    /** Lê o primeiro campo POST disponível e retorna só dígitos. */
    function ubezap_post_digits(array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($_POST[$key])) {
                continue;
            }
            $digits = ubezap_digits_only($_POST[$key]);
            if ($digits !== '') {
                return $digits;
            }
        }
        return '';
    }
}

if (!function_exists('ubezap_sql_digits_expr')) {
    /** Expressão SQL que remove pontuação comum de CPF/telefone. */
    function ubezap_sql_digits_expr($column)
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column, '.', ''), '-', ''), ' ', ''), '(', ''), ')', ''), '/', '')";
    }
}
