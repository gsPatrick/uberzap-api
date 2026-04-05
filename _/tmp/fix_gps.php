<?php
include("../bd/conexao.php");

try {
    $sql = "UPDATE motoristas SET latitude = -23.561706, longitude = -46.655981 WHERE id = 1002";
    $pdo->exec($sql);
    echo "✅ Motorista 1002 atualizado na Avenida Paulista!";
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
