<?php
include_once "../bd/conexao.php";
include("../classes/motoristas.php");

$m = new Motoristas();
$cpf = "999.888.777-00"; // O CPF que definimos

// 1. Buscar o ID do motorista pelo CPF
$sql = "SELECT id FROM motoristas WHERE cpf = :cpf";
$stmt = $pdo->prepare($sql);
$stmt->execute([':cpf' => $cpf]);
$driver = $stmt->fetch();

if ($driver) {
    $id = $driver['id'];
    
    // 2. Forçar coordenadas (perto do ponto de teste: Av. Paulista)
    $lat = -23.561706;
    $lng = -46.655981;
    
    $sql_update = "UPDATE motoristas SET 
        latitude = :lat, 
        longitude = :lng, 
        online = 1,
        ativo = 1,
        cidade_id = 1
        WHERE id = :id";
    
    $stmt_up = $pdo->prepare($sql_update);
    $stmt_up->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':id' => $id
    ]);
    
    echo "✅ Motorista ID $id atualizado!\n";
    echo "📍 Localização: $lat, $lng\n";
    echo "🟢 Status: Ativo e Online\n";
} else {
    echo "❌ Motorista com CPF $cpf não encontrado.\n";
}
?>
