<?php
include("../bd/config.php");
include("../classes/seguranca.php");

$secret_key = $_POST['secret'];
$s = new seguranca();

if ($s->compare_secret($secret_key)) {
    include("../bd/conexao.php");
    $id_motorista = $_POST['id_motorista'];
    $id_corrida = $_POST['id_corrida'];

    if (!$id_motorista || !$id_corrida) {
        echo "error_params";
        exit;
    }

    try {
        $sql_schema = "CREATE TABLE IF NOT EXISTS corridas_rejeitadas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_motorista INT NOT NULL,
            id_corrida INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_motorista_corrida (id_motorista, id_corrida),
            INDEX idx_motorista (id_motorista),
            INDEX idx_corrida (id_corrida)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $pdo->exec($sql_schema);

        $sql = "INSERT INTO corridas_rejeitadas (id_motorista, id_corrida) VALUES (:id_motorista, :id_corrida)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_motorista' => $id_motorista, 'id_corrida' => $id_corrida]);
        echo "ok";
    } catch (Exception $e) {
        // Se já existir (duplicado), ignora e retorna ok.
        // Outros erros devem ser informados para não mascarar falhas de recusa.
        $msg = $e->getMessage();
        if (strpos($msg, '1062') !== false || strpos(strtolower($msg), 'duplicate') !== false) {
            echo "ok";
        } else {
            echo "error_db";
        }
    }
} else {
    echo "no_auth";
}
?>
