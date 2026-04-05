<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../bd/conexao.php");

try {
    $sql = "CREATE TABLE IF NOT EXISTS corridas_rejeitadas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_motorista INT NOT NULL,
        id_corrida INT NOT NULL,
        data_rejeicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (id_motorista),
        INDEX (id_corrida)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    echo "✅ Tabela 'corridas_rejeitadas' criada ou já existente!";
} catch (PDOException $e) {
    echo "❌ Erro ao criar tabela: " . $e->getMessage();
}
?>
