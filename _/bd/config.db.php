<?php
/**
 * Sobrescreva credenciais do banco neste arquivo no servidor (opcional).
 *
 * CENÁRIO MAIS COMUM NO EASYPANEL:
 * MySQL na mesma VPS que o PHP — use 127.0.0.1, NÃO o IP público.
 *
 *   $hostname = '127.0.0.1';
 *   $port = 1212;
 *
 * MySQL como serviço interno no Easypanel (outro container):
 *
 *   $hostname = 'nome-do-servico-mysql';  // ex: mysql, uberzapbd
 *   $port = 3306;
 *
 * Ou defina variáveis de ambiente no painel:
 *   DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME
 */

// Descomente e ajuste no servidor de produção:
// $hostname = '127.0.0.1';
// $port = 1212;
