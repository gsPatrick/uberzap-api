<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once "../bd/conexao.php"; // garante $pdo + $secret
include_once "../classes/seguranca.php";
include_once "../classes/motoristas.php";
include_once "../classes/transacoes_motoristas.php";
include_once "../classes/tempo.php";

$s = new seguranca();
if (!$s->compare_secret($_POST['secret'] ?? '')) {
    http_response_code(401);
    echo json_encode(['saldo' => '0,00', 'transacoes' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$m = new Motoristas();
$tmp = new tempo();

// O app envia id_motorista (mantém motorista_id como fallback legado)
$motorista_id = (int) ($_POST['id_motorista'] ?? $_POST['motorista_id'] ?? 0);
$dados_motorista = $motorista_id > 0 ? $m->get_motorista($motorista_id) : null;
$cidade_id = $dados_motorista['cidade_id'] ?? 0;
$saldo = $dados_motorista['saldo'] ?? '0,00';

$transacoes = array();
if ($dados_motorista) {
    $t = new transacoes_motoristas($cidade_id);
    $lista = $t->getByUserId($motorista_id);
    if ($lista && is_array($lista)) {
        foreach ($lista as $transacao) {
            $transacao['date'] = $tmp->data_mysql_para_user($transacao['date'] ?? '')
                . " " . $tmp->hora_mysql_para_user($transacao['date'] ?? '');
            // Campos derivados pra compatibilidade com o app (espera descricao/tipo)
            $metodo = (string) ($transacao['metodo'] ?? '');
            $transacao['descricao'] = $metodo !== '' ? $metodo : 'Transação';
            $transacao['tipo'] = preg_match('/(d[ée]bito|saque|debit)/i', $metodo) ? 'Saque' : 'Credito';
            $transacoes[] = $transacao;
        }
    }
}

echo json_encode(array(
    'saldo' => $saldo,
    'transacoes' => $transacoes,
), JSON_UNESCAPED_UNICODE);
