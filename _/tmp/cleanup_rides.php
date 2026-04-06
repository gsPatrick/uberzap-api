<?php
include_once "../classes/corridas.php";
$c = new corridas();

// Vamos cancelar todas as corridas que estão com status 0 (Procurando) 
// que NÃO sejam a nossa de 90 reais (ID 531)
$sql = "UPDATE corridas SET status = '5' WHERE status = '0' AND id != 531";
$pdo = new conexao(); // Usando a conexão global
$stmt = $pdo->prepare($sql);
$stmt->execute();

echo "LIMPEZA CONCLUÍDA! Corridas antigas canceladas. Agora a de R$ 90,00 deve subir primeiro.";
?>
