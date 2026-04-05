<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../bd/conexao.php");

try {
    $pdo->query("SELECT 1 FROM corridas_rejeitadas LIMIT 1");
    echo "✅ Tabela existe!";
} catch (Exception $e) {
    echo "❌ Tabela NÃO existe. Tentando criar agora...\n";
    try {
        $sql = "CREATE TABLE IF NOT EXISTS corridas_rejeitadas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_motorista INT NOT NULL,
            id_corrida INT NOT NULL,
            data_rejeicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($sql);
        echo "✅ Tabela criada com sucesso!";
    } catch (Exception $e2) {
        echo "🔥 Erro fatal: " . $e2->getMessage();
    }
}
?>
