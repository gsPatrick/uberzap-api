<?php
include("../bd/config.php");

$id_motorista = 1002;
$cidade_id = 1;
$secret = "abc1234";

echo "--- Depurando Match de Corrida ---\n";

// Simular chamada que o App faria
$url = "https://geral-uberzap-api.r954jc.easypanel.host/_/motoristas/busca_corridas_disponiveis.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=$secret&id_motorista=$id_motorista&cidade_id=$cidade_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Resposta da API: " . $response . "\n";

if ($response === "no") {
    echo "⚠️ O servidor disse que NÃO há corridas disponíveis para este motorista.\n";
    
    // Investigar por que
    include_once "../classes/motoristas.php";
    include_once "../classes/corridas.php";
    
    $m = new Motoristas();
    $c = new Corridas();
    
    $dados_m = $m->get_motorista($id_motorista);
    echo "Dados do Motorista:\n";
    echo "- Online: " . $dados_m['online'] . " (1=Online, 2=Offline)\n";
    echo "- Categorias: " . $dados_m['ids_categorias'] . "\n";
    echo "- Localização: " . $dados_m['latitude'] . ", " . $dados_m['longitude'] . "\n";
    
    $corridas = $c->get_corridas_disponiveis($cidade_id);
    if ($corridas) {
        foreach ($corridas as $rd) {
            echo "Corrida Disponível no Banco: ID " . $rd['id'] . " | Cat: " . $rd['categoria_id'] . "\n";
        }
    } else {
        echo "❌ Nenhuma corrida com status 0 na Cidade $cidade_id.\n";
    }
} else {
    echo "✅ O servidor RETORNOU a corrida! O problema está na atualização do App (iOS).\n";
}
?>
