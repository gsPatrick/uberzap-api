<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/normalize.php';

include('../classes/motoristas.php');
include('../classes/seguranca.php');
include('../classes/cidades.php');
include('../classes/taximetro.php');

try {
    $secret_key = $_POST['secret'] ?? '';
    $s = new seguranca();
    $c = new Cidades();
    $t = new Taximetro();

    if (!$s->compare_secret($secret_key)) {
        http_response_code(401);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha na autenticação, verifique a secret em bd/conexao.php',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Aceita CPF/telefone com máscara: 869.318.445-80, (86) 93184-4580, etc.
    $cpf = ubezap_post_digits(['cpf', 'telefone', 'login', 'usuario', 'documento']);
    $senha = (string) ($_POST['senha'] ?? '');

    if ($cpf === '' || $senha === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'CPF e senha são obrigatórios.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $salt = 'anjdsn5s141d5';
    $senha = md5($senha . $salt);

    $m = new Motoristas();
    $login = $m->login_motorista($cpf, $senha);

    if (!$login) {
        http_response_code(401);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Usuario ou senha incorretos',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = $login['id'];
    if (!$m->verifica_se_esta_ativo($id)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Motorista bloqueado, entre em contato com a central',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $dados_cidade = $c->get_dados_cidade($login['cidade_id']);
    $login['latitude_cidade'] = $dados_cidade['latitude'] ?? null;
    $login['longitude_cidade'] = $dados_cidade['longitude'] ?? null;

    $taxas = $t->getByCidadeId($login['cidade_id']);
    if ($taxas) {
        $login['taxi_tx_minima'] = str_replace(',', '.', $taxas['tx_minima']);
        $login['taxi_tx_minuto'] = str_replace(',', '.', $taxas['tx_minuto']);
        $login['taxi_tx_km'] = str_replace(',', '.', $taxas['tx_km']);
    } else {
        $login['taxi_tx_minima'] = 0;
        $login['taxi_tx_minuto'] = 0;
        $login['taxi_tx_km'] = 0;
    }

    echo json_encode($login, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[motoristas/login.php] ' . $e->getMessage());
    http_response_code(503);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro temporário no servidor. Tente novamente em alguns segundos.',
    ], JSON_UNESCAPED_UNICODE);
}
