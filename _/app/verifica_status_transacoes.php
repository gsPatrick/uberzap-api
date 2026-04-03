<?php
header('access-control-allow-origin: *');
include_once "../classes/transacoes.php";
include '../classes/configuracoes_pagamento.php';
include_once "../classes/clientes.php";

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

// Valida entrada mínima
if (!isset($_POST['cidade_id'], $_POST['user_id'])) {
    exit("Dados incompletos.");
}

$cidade_id = (int) $_POST['cidade_id'];
$user_id = (int) $_POST['user_id'];

$t = new transacoes($cidade_id);
$cp = new configuracoes_pagamento();
$c = new Clientes();

$token = $cp->read_configuracoes_pagamento($cidade_id)['token'] ?? null;
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

        $saldo_atual = str_replace(",", ".", $c->get_cliente_id($user_id)['saldo']);
        $saldo_adicional = str_replace(",", ".", $transacao['valor']);
        $novo_saldo = number_format($saldo_atual + $saldo_adicional, 2, ',', '');

        $c->atualiza_saldo($user_id, $novo_saldo);
        $alterou = true;
    }
}

if($alterou){
    echo json_encode(array('status' => 'ok'));
}else{
    echo json_encode(array('status' => 'no'));
}
?>