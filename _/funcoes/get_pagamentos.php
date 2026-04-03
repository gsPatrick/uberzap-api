<?php

header("Access-Control-Allow-Origin: *");

require_once '../classes/corridas.php';
require_once '../classes/configuracoes_pagamento.php';
require_once '../classes/status_historico.php';

/**
 * Classe responsável por verificar o status de um pagamento via API externa
 */
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
if (!isset($_POST['cidade_id']) && !isset($_SESSION['cidade_id'])) {
    exit("Parâmetro cidade_id ausente.");
}

$cidade_id = isset($_POST['cidade_id']) ? (int) $_POST['cidade_id'] : (int) $_SESSION['cidade_id'];

$p = new corridas();
$cp = new configuracoes_pagamento();
$sh = new status_historico();

$config = $cp->read_configuracoes_pagamento($cidade_id);
$token = $config['token'] ?? null;

if (!$token) {
    exit("Token de pagamento não encontrado.");
}

$pedidos = $p->get_all_corridas_cidade($cidade_id);
$alterou = false;

foreach ($pedidos as $pedido) {
    if (
        $pedido['pagamento'] !== "Pagamento Online" ||
        $pedido['status_pagamento'] !== "PENDING" ||
        empty($pedido['ref_pagamento'])
    ) {
        continue;
    }

    $verificador = new PagamentoVerificador([
        'token' => $token,
        'ref'   => $pedido['ref_pagamento']
    ]);

    $resposta = $verificador->verificar();
    $status_pagamento = $resposta['status'] ?? null;

    if (in_array($status_pagamento, ['Aprovado', 'Autorizado'])) {
        $p->update_status_pagamento($pedido['id'], $status_pagamento);
        $sh->salva_status($pedido['id'], "Pagamento Online Recebido");
        $alterou = true;
    }
}

echo $alterou ? "1" : "0";
