<?php
/**
 * Script de Instalação e Atualização de Banco de Dados - UbeZap
 * Finalidade: Adicionar colunas e tabelas necessárias para a Sprint 1.2
 */

require_once '../bd/conexao.php';

function executeQuery($pdo, $sql, $description)
{
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>[SUCESSO] $description</p>";
    }
    catch (PDOException $e) {
        if ($e->getCode() == '42S21' || $e->getCode() == '42S01') {
            echo "<p style='color: orange;'>[AVISO] $description (Já existe)</p>";
        }
        else {
            echo "<p style='color: red;'>[ERRO] $description: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h2>Iniciando Atualização do Banco de Dados...</h2>";

// 1. Atualizar tabela motorista_docs para Antecedentes Criminais
executeQuery($pdo, "ALTER TABLE motorista_docs ADD COLUMN img_antecedentes VARCHAR(255) DEFAULT NULL AFTER img_selfie", "Coluna 'img_antecedentes' em 'motorista_docs'");

// 2. Atualizar tabela categorias para Taxa de Espera
executeQuery($pdo, "ALTER TABLE categorias ADD COLUMN tx_espera DECIMAL(10,2) DEFAULT '0.00' AFTER tx_base", "Coluna 'tx_espera' em 'categorias'");

// 3. Atualizar tabela taximetro para Taxa de Espera (Painel Administrativo)
executeQuery($pdo, "ALTER TABLE taximetro ADD COLUMN tx_espera DECIMAL(10,2) DEFAULT '0.00' AFTER tx_km", "Coluna 'tx_espera' em 'taximetro'");

// 4. Atualizar tabela corridas para Lógica de Espera e Cancelamento
$sqlCorridas = "ALTER TABLE corridas 
    ADD COLUMN inicio_espera DATETIME DEFAULT NULL,
    ADD COLUMN tempo_espera INT DEFAULT 0,
    ADD COLUMN valor_espera DECIMAL(10,2) DEFAULT '0.00',
    ADD COLUMN taxa_cancelamento DECIMAL(10,2) DEFAULT '0.00'";
executeQuery($pdo, $sqlCorridas, "Colunas de espera e cancelamento em 'corridas'");

// 5. Criar tabela de Logs de Cancelamento (Opcional mas recomendado)
$sqlLogCancel = "CREATE TABLE IF NOT EXISTS log_cancelamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    corrida_id INT,
    usuario_tipo ENUM('motorista', 'cliente', 'sistema'),
    motivo TEXT,
    taxa_aplicada DECIMAL(10,2),
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
executeQuery($pdo, $sqlLogCancel, "Tabela 'log_cancelamentos'");

echo "<hr>";
echo "<p><b>Instalação Concluída!</b></p>";
echo "<p style='color: red;'><b>IMPORTANTE: Delete este arquivo (install.php) do servidor por segurança.</b></p>";
?>
