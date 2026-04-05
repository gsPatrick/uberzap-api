<?php
include_once "../bd/conexao.php";

$passenger_phone = "71982862912";
$new_driver_phone = "11988887777"; // Número aleatório para o motorista
$new_driver_cpf = "999.888.777-00";

echo "--- Criando Motorista de Teste ---\n";

// 1. Buscar senha do passageiro
$sql = "SELECT senha, cidade_id FROM clientes WHERE telefone = :phone";
$stmt = $pdo->prepare($sql);
$stmt->execute([':phone' => $passenger_phone]);
$passenger = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$passenger) {
    die("❌ Passageiro $passenger_phone não encontrado no banco.\n");
}

$senha_hash = $passenger['senha'];
$cidade_id = $passenger['cidade_id'];

// 2. Inserir novo motorista
try {
    $sql_insert = "INSERT INTO motoristas (
        cidade_id, nome, email, cpf, img, veiculo, placa, telefone, senha, taxa, saldo, ids_categorias, ativo, online
    ) VALUES (
        :cidade_id, 'Motorista de Teste', 'driver@test.com', :cpf, 'default.jpg', 'Carro de Teste', 'TST-2026', :phone, :senha, 15, 100.00, '[1,2,3]', 1, 0
    )";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':cidade_id' => $cidade_id,
        ':cpf' => $new_driver_cpf,
        ':phone' => $new_driver_phone,
        ':senha' => $senha_hash
    ]);
    
    echo "✅ Motorista criado com sucesso!\n";
    echo "📱 Telefone: $new_driver_phone\n";
    echo "🔑 Senha: (Mesma do passageiro)\n";
    echo "📍 Cidade ID: $cidade_id\n";
    
} catch (PDOException $e) {
    echo "❌ Erro ao criar motorista: " . $e->getMessage() . "\n";
}
?>
