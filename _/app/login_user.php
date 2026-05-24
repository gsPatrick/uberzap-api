<?php 
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');

require_once __DIR__ . '/../bd/normalize.php';

try {
    include_once '../classes/clientes.php';
    $clientes = new Clientes();

    $telefone = ubezap_post_digits(['telefone', 'cpf', 'login', 'usuario', 'celular']);
    $senha = (string) ($_POST['senha'] ?? '');

    if ($telefone === '' || $senha === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'erro',
            'erro' => 'Telefone e senha são obrigatórios.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $salt = 'anjdsn5s141d5';
    $senha = md5($senha . $salt);

    $cliente = $clientes->verifica_se_existe($telefone);

    if ($cliente && $cliente['senha'] == $senha) {
        $retorno = [
            'status' => 'sucesso',
            'id' => $cliente['id'],
            'nome' => $cliente['nome'],
            'email' => $cliente['email'],
            'telefone' => $cliente['telefone'],
            'nota' => '4.9',
            'ativo' => $cliente['ativo'],
            'saldo' => $cliente['saldo'],
            'cidade_id' => $cliente['cidade_id'],
        ];
    } else {
        http_response_code(401);
        $retorno = [
            'status' => 'erro',
            'erro' => 'Telefone ou senha incorretos',
        ];
    }

    echo json_encode($retorno, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[app/login_user.php] ' . $e->getMessage());
    http_response_code(503);
    echo json_encode([
        'status' => 'erro',
        'erro' => 'Erro temporário no servidor. Tente novamente em alguns segundos.',
    ], JSON_UNESCAPED_UNICODE);
}
