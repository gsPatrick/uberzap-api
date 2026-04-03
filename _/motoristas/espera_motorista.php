<?php
/**
 * API para controle de tempo de espera do motorista
 */

require_once '../classes/corridas.php';
require_once '../classes/seguranca.php';

$corrida_id = $_POST['corrida_id'];
$acao = $_POST['acao']; // 'iniciar' ou 'finalizar'
$secret = $_POST['secret'];

$seguranca = new seguranca();
if ($secret != $seguranca->getSecret()) {
    die(json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado']));
}

$corridas = new corridas();

if ($acao == 'iniciar') {
    if ($corridas->setInicioEspera($corrida_id)) {
        echo json_encode(['status' => 'sucesso', 'mensagem' => 'Espera iniciada']);
    }
    else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao iniciar espera']);
    }
}
elseif ($acao == 'finalizar') {
    if ($corridas->finalizaEspera($corrida_id)) {
        $dados = $corridas->get_corrida_id($corrida_id);
        echo json_encode([
            'status' => 'sucesso',
            'mensagem' => 'Espera finalizada',
            'tempo_espera' => $dados['tempo_espera'],
            'valor_espera' => $dados['valor_espera'],
            'taxa_total' => $dados['taxa']
        ]);
    }
    else {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao finalizar espera']);
    }
}
?>