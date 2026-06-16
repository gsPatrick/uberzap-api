<?php
header('Content-Type: application/json; charset=utf-8');
header('access-control-allow-origin: *');
require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/seguranca.php';
require_once __DIR__ . '/../classes/motoristas.php';

try {
    $secret_key = $_POST['secret'] ?? '';
    $s = new seguranca();
    if (!$s->compare_secret($secret_key)) {
        http_response_code(401);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Falha na autenticação, verifique a secret em bd/conexao.php',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id          = (int) ($_POST['id_motorista'] ?? 0);
    $senha_atual = (string) ($_POST['senha_atual'] ?? '');
    $nova_senha  = (string) ($_POST['nova_senha'] ?? '');

    if ($id <= 0 || $senha_atual === '' || $nova_senha === '') {
        http_response_code(400);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Preencha a senha atual e a nova senha.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (strlen($nova_senha) < 4) {
        http_response_code(400);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'A nova senha deve ter pelo menos 4 caracteres.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Mesmo salt/hash usados em motoristas/login.php.
    $salt = 'anjdsn5s141d5';

    $m = new Motoristas();
    $motorista = $m->get_motorista($id);

    // get_motorista retorna id=0 quando não encontra (nunca false).
    if (!$motorista || (int) ($motorista['id'] ?? 0) === 0) {
        http_response_code(404);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Motorista não encontrado.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (md5($senha_atual . $salt) !== ($motorista['senha'] ?? '')) {
        http_response_code(401);
        echo json_encode([
            'status' => 'erro',
            'mensagem' => 'Senha atual incorreta.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $m->redefinir_senha($id, md5($nova_senha . $salt));

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => 'Senha alterada com sucesso.',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'erro',
        'mensagem' => 'Erro ao alterar a senha.',
    ], JSON_UNESCAPED_UNICODE);
}
