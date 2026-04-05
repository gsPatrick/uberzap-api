<?php
include("../bd/config.php");
include("../bd/conexao.php");

// Apaga todas as corridas do motorista 1002 para limpeza de ambiente
$stmt = $pdo->prepare("DELETE FROM corridas WHERE motorista_id = 1002");
$stmt->execute();

echo "Limpeza concluída: " . $stmt->rowCount() . " corridas apagadas.";
?>
