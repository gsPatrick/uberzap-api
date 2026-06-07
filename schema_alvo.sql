-- UbeZap • Schema-alvo SOMENTE DDL (sem dados). Gerado por migrate.php --emit
-- Seguro para subir no servidor: não contém nenhum INSERT/dado de usuário.

-- ---- admin ----
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL,
  `user` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `cidade_id` int(11) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `admin` ADD PRIMARY KEY (`id`);
ALTER TABLE `admin` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- alertas_motoristas ----
CREATE TABLE IF NOT EXISTS `alertas_motoristas` (
  `id` int(11) NOT NULL,
  `id_motorista` int(11) NOT NULL,
  `msg` varchar(1000) NOT NULL,
  `executado` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `alertas_motoristas` ADD PRIMARY KEY (`id`);
ALTER TABLE `alertas_motoristas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- avaliacoes ----
CREATE TABLE IF NOT EXISTS `avaliacoes` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `motorista_id` int(11) DEFAULT NULL,
  `nota` varchar(255) DEFAULT NULL,
  `comentario` varchar(1000) DEFAULT NULL,
  `corrida_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `avaliacoes` ADD PRIMARY KEY (`id`);
ALTER TABLE `avaliacoes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- banners ----
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `ativo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `banners` ADD PRIMARY KEY (`id`);
ALTER TABLE `banners` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- categorias ----
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` varchar(255) NOT NULL DEFAULT '',
  `dinamico_horarios` varchar(1000) NOT NULL,
  `dinamico_local` varchar(1000) NOT NULL,
  `img` varchar(255) NOT NULL DEFAULT 'sem_imagem.png',
  `tx_km` varchar(255) DEFAULT NULL,
  `tx_minuto` varchar(255) DEFAULT NULL,
  `tx_minima` varchar(255) DEFAULT NULL,
  `tx_base` varchar(255) DEFAULT NULL,
  `raio` varchar(255) DEFAULT NULL,
  `ativa` int(11) NOT NULL DEFAULT 1,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `categorias` ADD PRIMARY KEY (`id`);
ALTER TABLE `categorias` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- cidades ----
CREATE TABLE IF NOT EXISTS `cidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL DEFAULT '',
  `latitude` varchar(255) NOT NULL DEFAULT '',
  `longitude` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `cidades` ADD PRIMARY KEY (`id`);
ALTER TABLE `cidades` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- clientes ----
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `id_signal` varchar(1000) DEFAULT '0',
  `ativo` int(11) NOT NULL DEFAULT 1,
  `saldo` varchar(255) NOT NULL DEFAULT '0',
  `senha` varchar(1000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `clientes` ADD PRIMARY KEY (`id`);
ALTER TABLE `clientes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- cobranca ----
CREATE TABLE IF NOT EXISTS `cobranca` (
  `id_franqueado` int(11) NOT NULL,
  `tipo_cobranca` int(11) NOT NULL,
  `saldo` varchar(100) NOT NULL DEFAULT '0,00',
  `valor_desconto` varchar(100) NOT NULL DEFAULT '100',
  `porcentagem` int(11) NOT NULL DEFAULT 0,
  `valor_mensal` varchar(100) NOT NULL DEFAULT '0,00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- ---- configuracoes_pagamento ----
CREATE TABLE IF NOT EXISTS `configuracoes_pagamento` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `token` varchar(1000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `configuracoes_pagamento` ADD PRIMARY KEY (`id`);
ALTER TABLE `configuracoes_pagamento` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- corridas ----
CREATE TABLE IF NOT EXISTS `corridas` (
  `id` int(11) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `motorista_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `cliente_id` int(11) NOT NULL DEFAULT 0,
  `cidade_id` int(11) NOT NULL,
  `lat_ini` varchar(255) NOT NULL,
  `lng_ini` varchar(255) NOT NULL,
  `lat_fim` varchar(255) NOT NULL,
  `lng_fim` varchar(255) NOT NULL,
  `km` varchar(20) NOT NULL,
  `tempo` varchar(20) NOT NULL,
  `endereco_ini_txt` varchar(300) NOT NULL,
  `endereco_fim_txt` varchar(300) NOT NULL,
  `taxa` varchar(50) NOT NULL,
  `f_pagamento` varchar(255) NOT NULL DEFAULT '',
  `status_pagamento` varchar(255) NOT NULL DEFAULT '',
  `ref_pagamento` varchar(255) NOT NULL DEFAULT '',
  `cupom` varchar(255) NOT NULL DEFAULT '0',
  `categoria_id` int(11) NOT NULL DEFAULT 0,
  `nome_cliente` varchar(255) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT 0,
  `user_whatsapp` varchar(255) NOT NULL DEFAULT '',
  `img_user` varchar(255) NOT NULL DEFAULT '',
  `obs` varchar(1000) NOT NULL DEFAULT 'Não Informado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `corridas` ADD PRIMARY KEY (`id`);
ALTER TABLE `corridas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- cupons ----
CREATE TABLE IF NOT EXISTS `cupons` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `valor` varchar(250) NOT NULL,
  `valor_min` varchar(250) NOT NULL,
  `primeira_compra` int(11) NOT NULL,
  `validade` datetime NOT NULL,
  `quantidade` int(11) NOT NULL,
  `uso_unico` int(11) NOT NULL DEFAULT 0,
  `tipo_desconto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `cupons` ADD PRIMARY KEY (`id`);
ALTER TABLE `cupons` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- cupons_usados ----
CREATE TABLE IF NOT EXISTS `cupons_usados` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `cupons_usados` ADD PRIMARY KEY (`id`);
ALTER TABLE `cupons_usados` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- dinamico_horarios ----
CREATE TABLE IF NOT EXISTS `dinamico_horarios` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `segunda` varchar(255) DEFAULT NULL,
  `terca` varchar(255) DEFAULT NULL,
  `quarta` varchar(255) DEFAULT NULL,
  `quinta` varchar(255) DEFAULT NULL,
  `sexta` varchar(255) DEFAULT NULL,
  `sabado` varchar(255) DEFAULT NULL,
  `domingo` varchar(255) DEFAULT NULL,
  `adicional` varchar(255) NOT NULL DEFAULT '0,00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `dinamico_horarios` ADD PRIMARY KEY (`id`);
ALTER TABLE `dinamico_horarios` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- dinamico_mapa ----
CREATE TABLE IF NOT EXISTS `dinamico_mapa` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `raio` varchar(255) NOT NULL DEFAULT '0',
  `adicional` varchar(255) NOT NULL DEFAULT '0,00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `dinamico_mapa` ADD PRIMARY KEY (`id`);
ALTER TABLE `dinamico_mapa` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- franqueados ----
CREATE TABLE IF NOT EXISTS `franqueados` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `comissao` int(11) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `franqueados` ADD PRIMARY KEY (`id`);
ALTER TABLE `franqueados` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- localizacao_corridas ----
CREATE TABLE IF NOT EXISTS `localizacao_corridas` (
  `id` int(11) NOT NULL,
  `corrida_id` int(11) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `localizacao_corridas` ADD PRIMARY KEY (`id`);
ALTER TABLE `localizacao_corridas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- motoristas ----
CREATE TABLE IF NOT EXISTS `motoristas` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL DEFAULT 0,
  `nome` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `veiculo` varchar(255) DEFAULT NULL,
  `placa` varchar(255) DEFAULT NULL,
  `online` int(11) NOT NULL DEFAULT 0,
  `ativo` varchar(255) DEFAULT '1',
  `latitude` varchar(255) DEFAULT '0',
  `longitude` varchar(255) DEFAULT '0',
  `telefone` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `nota` varchar(255) DEFAULT '0',
  `id_signal` varchar(1000) DEFAULT '',
  `taxa` varchar(255) DEFAULT NULL,
  `saldo` varchar(255) NOT NULL DEFAULT '0,00',
  `ids_categorias` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`ids_categorias`)),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `motoristas` ADD PRIMARY KEY (`id`);
ALTER TABLE `motoristas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- motorista_docs ----
CREATE TABLE IF NOT EXISTS `motorista_docs` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `img_antecedente` varchar(255) NOT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `telefone` varchar(255) DEFAULT NULL,
  `veiculo` varchar(255) DEFAULT NULL,
  `placa` varchar(255) DEFAULT NULL,
  `img_cnh` varchar(255) DEFAULT NULL,
  `img_documento` varchar(255) DEFAULT NULL,
  `img_lateral` varchar(255) DEFAULT NULL,
  `img_frente` varchar(255) DEFAULT NULL,
  `img_selfie` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `motorista_docs` ADD PRIMARY KEY (`id`);
ALTER TABLE `motorista_docs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- msg ----
CREATE TABLE IF NOT EXISTS `msg` (
  `id` int(11) NOT NULL,
  `id_corrida` int(11) DEFAULT NULL,
  `msg` varchar(1500) DEFAULT NULL,
  `sender` int(11) NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `msg` ADD PRIMARY KEY (`id`);
ALTER TABLE `msg` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- otp_verifications ----
CREATE TABLE IF NOT EXISTS `otp_verifications` (
  `id` int(11) NOT NULL,
  `numero_telefone` varchar(255) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `otp_verifications` ADD PRIMARY KEY (`id`);
ALTER TABLE `otp_verifications` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- status_historico ----
CREATE TABLE IF NOT EXISTS `status_historico` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `hora` varchar(255) NOT NULL,
  `local` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `status_historico` ADD PRIMARY KEY (`id`);
ALTER TABLE `status_historico` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- taximetro ----
CREATE TABLE IF NOT EXISTS `taximetro` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `tx_minima` varchar(255) NOT NULL,
  `tx_minuto` varchar(255) NOT NULL,
  `tx_km` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `taximetro` ADD PRIMARY KEY (`id`);
ALTER TABLE `taximetro` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- temp_id ----
CREATE TABLE IF NOT EXISTS `temp_id` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `ultimo_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `temp_id` ADD PRIMARY KEY (`id`);
ALTER TABLE `temp_id` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- transacoes ----
CREATE TABLE IF NOT EXISTS `transacoes` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `link` varchar(1500) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `transacoes` ADD PRIMARY KEY (`id`);
ALTER TABLE `transacoes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- transacoes_motoristas ----
CREATE TABLE IF NOT EXISTS `transacoes_motoristas` (
  `id` int(11) NOT NULL,
  `cidade_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `link` varchar(1000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `transacoes_motoristas` ADD PRIMARY KEY (`id`);
ALTER TABLE `transacoes_motoristas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- ---- usuarios_bot_whats ----
CREATE TABLE IF NOT EXISTS `usuarios_bot_whats` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `msg_recebida` varchar(255) NOT NULL,
  `msg_enviada` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
ALTER TABLE `usuarios_bot_whats` ADD PRIMARY KEY (`id`);
ALTER TABLE `usuarios_bot_whats` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

