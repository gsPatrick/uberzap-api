<?php
header('access-control-allow-origin: *');
include_once "../classes/transacoes_motoristas.php";
include '../classes/configuracoes_pagamento.php';
include_once "../classes/motoristas.php";

class PagamentoVerificador
{
    private $endpoint = "https://api.pagmp.com/api/verifica_pagamento.php";
    private $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    public function verificar(): ?array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($this->dados),
        ]);

        $resposta = curl_exec($curl);
        curl_close($curl);

        if (!$resposta) {
            return null;
        }

        return json_decode($resposta, true);
    }
}

// Validação básica
if (!isset($_POST['cidade_id'], $_POST['user_id'])) {
    exit("Parâmetros obrigatórios ausentes.");
}

$cidade_id = (int) $_POST['cidade_id'];
$user_id = (int) $_POST['user_id'];

$t = new transacoes_motoristas($cidade_id);
$cp = new configuracoes_pagamento();
$m = new Motoristas();

$config = $cp->read_configuracoes_pagamento($cidade_id);
$token = $config['token'] ?? null;

if (!$token) {
    exit("Token de pagamento não encontrado.");
}

$transacoes = $t->getByUserId($user_id);
$alterou = false;

foreach ($transacoes as $transacao) {
    if ($transacao['status'] !== "PENDENTE") {
        continue;
    }

    $dadosEnvio = [
        'token' => $token,
        'ref'   => $transacao['ref']
    ];

    $verificador = new PagamentoVerificador($dadosEnvio);
    $resposta = $verificador->verificar();

    if ($resposta && in_array($resposta['status'], ['Aprovado', 'Autorizado'])) {
        $t->atualizaStatusId($transacao['id'], "CONCLUIDO");

        $saldo_atual = str_replace(",", ".", $m->get_motorista($user_id)['saldo']);
        $saldo_adicional = str_replace(",", ".", $transacao['valor']);
        $saldo_novo = number_format($saldo_atual + $saldo_adicional, 2, ',', '');

        $m->atualiza_saldo($user_id, $saldo_novo);
        $alterou = true;
    }
}

if($alterou){
    echo json_encode(array('status' => 'ok'));
}else{
    echo json_encode(array('status' => 'no'));
}
?>