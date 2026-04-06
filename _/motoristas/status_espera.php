<?php
/**
 * Script para controlar o status de espera da corrida (UberZap API)
 */
header('Content-Type: application/json');
require_once '../classes/corridas.php';

$id_corrida = $_POST['id_corrida'] ?? null;
$acao = $_POST['acao'] ?? null; // 'iniciar' ou 'finalizar'

if (!$id_corrida || !$acao) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Parametros insuficientes']);
    exit;
}

$corridas = new corridas();

if ($acao === 'iniciar') {
    $res = $corridas->setInicioEspera($id_corrida);
    if ($res) {
        echo json_encode(['status' => 'ok', 'mensagem' => 'Contagem de espera iniciada']);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao iniciar espera no banco']);
    }
} else if ($acao === 'finalizar') {
    $res = $corridas->finalizaEspera($id_corrida);
    if ($res) {
        // Retorna a corrida atualizada para o app ver o novo valor
        $dados = $corridas->get_corrida_id($id_corrida);
        echo json_encode([
            'status' => 'ok', 
            'mensagem' => 'Espera finalizada', 
            'valor_espera' => $dados['valor_espera'] ?? 0,
            'tempo_espera' => $dados['tempo_espera'] ?? 0,
            'taxa_total' => $dados['taxa'] ?? 0
        ]);
    } else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Falha ao finalizar espera ou sem registro de inicio']);
    }
} else {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acao invalida']);
}
