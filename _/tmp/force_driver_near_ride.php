<?php
include_once "../bd/conexao.php";

$driver_phone = "11988887777"; // Telefone do motorista de teste

echo "--- Forçando Localização do Motorista ($driver_phone) ---\n";

// 1. Buscar o motorista pelo telefone para pegar o ID correto
$stmt = $pdo->prepare("SELECT id FROM motoristas WHERE telefone = :phone");
$stmt->execute([':phone' => $driver_phone]);
$driver = $stmt->fetch(PDO::FETCH_ASSOC);

if ($driver) {
    $id = $driver['id'];
    
    // 2. Forçar coordenadas (Av. Paulista) e status Online
    $lat = -23.561706;
    $lng = -46.655981;
    
    $sql_update = "UPDATE motoristas SET 
        latitude = :lat, 
        longitude = :lng, 
        online = 1,
        ativo = 1,
        ids_categorias = '[1,2,3]'
        WHERE id = :id";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':id' => $id
    ]);
    
    echo "✅ Motorista ID $id atualizado!\n";
    echo "📍 Localização: $lat, $lng\n";
    echo "🟢 Status: Ativo, Online e com Categorias [1,2,3]\n";
} else {
    echo "❌ Erro: Motorista com telefone $driver_phone não encontrado no banco!\n";
}
?>
