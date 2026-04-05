<?php
include_once "../bd/conexao.php";
$sql = "SELECT telefone, senha FROM clientes ORDER BY id DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $user) {
    echo "Tel: " . $user['telefone'] . " | Senha (Raw/MD5): " . $user['senha'] . "\n";
}
?>
