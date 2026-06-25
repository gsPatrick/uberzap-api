-- =====================================================================
-- UbeZap — Índices de performance
-- =====================================================================
-- O banco hoje só tem PRIMARY KEY (id) em cada tabela. Sem índices
-- secundários, todo filtro por cliente_id / motorista_id / cidade_id /
-- status / date / user_id / corrida_id / telefone / cpf faz FULL TABLE
-- SCAN. Como o app faz polling de status (4s) e de corridas (5s), isso
-- é o que mais pesa no servidor e na latência (inclusive ao abrir o app:
-- login + primeiro status).
--
-- Este script é IDEMPOTENTE: usa um procedure auxiliar que só cria o
-- índice se ele ainda não existir. Pode rodar quantas vezes quiser.
--
-- Como rodar (exemplos):
--   mysql -h 195.250.26.221 -u uberzapapp_transporte -p uberzapapp_transporte < otimizacao-indices.sql
--   (ou cole o conteúdo no cliente SQL do EasyPanel / phpMyAdmin)
--
-- Cada CREATE INDEX é rápido nestas tabelas; rode preferencialmente fora
-- do horário de pico. Não altera dados, só adiciona índices.
-- =====================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS __uz_add_idx $$
CREATE PROCEDURE __uz_add_idx(IN p_tbl VARCHAR(64), IN p_idx VARCHAR(64), IN p_cols VARCHAR(255))
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name   = p_tbl
      AND index_name   = p_idx
  ) THEN
    SET @ddl = CONCAT('CREATE INDEX `', p_idx, '` ON `', p_tbl, '` (', p_cols, ')');
    PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;
    SELECT CONCAT('criado: ', p_idx) AS resultado;
  ELSE
    SELECT CONCAT('ja existe: ', p_idx) AS resultado;
  END IF;
END $$

DELIMITER ;

-- ── corridas (tabela mais quente: histórico + polling) ──────────────
CALL __uz_add_idx('corridas', 'idx_corridas_cliente',     '`cliente_id`, `id`');
CALL __uz_add_idx('corridas', 'idx_corridas_motorista',   '`motorista_id`, `date`');
CALL __uz_add_idx('corridas', 'idx_corridas_cidade_stat', '`cidade_id`, `status`, `date`');
CALL __uz_add_idx('corridas', 'idx_corridas_whatsapp',    '`user_whatsapp`, `status`');

-- ── avaliacoes (N+1 do histórico e do polling de nota) ──────────────
CALL __uz_add_idx('avaliacoes', 'idx_avaliacoes_corrida',   '`corrida_id`');
CALL __uz_add_idx('avaliacoes', 'idx_avaliacoes_motorista', '`motorista_id`');

-- ── transações (carteira / extrato) ────────────────────────────────
CALL __uz_add_idx('transacoes',            'idx_transacoes_user',     '`user_id`, `id`');
CALL __uz_add_idx('transacoes_motoristas', 'idx_transacoes_mot_user', '`user_id`, `id`');

-- ── mensagens (chat / polling) ─────────────────────────────────────
CALL __uz_add_idx('msg', 'idx_msg_corrida', '`id_corrida`, `id`');

-- ── localização das corridas (update de posição em corrida) ─────────
CALL __uz_add_idx('localizacao_corridas', 'idx_localizacao_corrida', '`corrida_id`, `date`');

-- ── login / lookups ────────────────────────────────────────────────
-- Obs.: hoje o login aplica REPLACE(telefone/cpf) no WHERE, o que é
-- "não-sargable" e o índice não é usado. Estes índices ajudam outras
-- buscas por telefone/cpf cru. Para acelerar o login de verdade, veja a
-- nota no final (normalizar telefone/cpf só-dígitos numa coluna própria).
CALL __uz_add_idx('clientes',   'idx_clientes_telefone',  '`telefone`');
CALL __uz_add_idx('motoristas', 'idx_motoristas_cpf',     '`cpf`');
CALL __uz_add_idx('motoristas', 'idx_motoristas_online',  '`cidade_id`, `ativo`, `online`');

DROP PROCEDURE IF EXISTS __uz_add_idx;

-- =====================================================================
-- Depois de rodar, confira:
--   SHOW INDEX FROM corridas;
--   EXPLAIN SELECT * FROM corridas WHERE cliente_id = 123 ORDER BY id DESC LIMIT 20;
-- (o EXPLAIN deve mostrar "Using index condition" / key = idx_corridas_cliente,
--  e NÃO "type: ALL" que é full scan)
-- =====================================================================
