<?php 
include "seguranca.php";
include_once '../classes/clientes.php';
include_once '../classes/transacoes.php';
include_once '../bd/conexao.php'; // Inclua a conexão com o banco de dados

$c = new clientes();
$t = new transacoes($cidade_id);

$dados_cliente = $c->get_cliente_id($_GET['id']);
$saldo_antigo = str_replace(",", ".", $dados_cliente['saldo']);
$id = $_GET['id'];

// Coletando dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$saldo = $_POST['saldo'];
$saldo = str_replace(",", ".", $saldo);

// Verifica se o checkbox para atualizar a senha foi marcado
if (isset($_POST['update_password']) && $_POST['update_password'] == 'on') {
    // Verifica se a senha foi preenchida
    if (!empty($_POST['senha'])) {
        $senha = md5($_POST['senha'] . "anjdsn5s141d5"); // Criptografa a nova senha
    } else {
        $senha = $dados_cliente['senha']; // Mantém a senha existente se não houver nova senha
    }
} else {
    // Se o checkbox não foi marcado, mantém a senha antiga
    $senha = $dados_cliente['senha'];
}

// Atualiza o saldo e realiza as transações necessárias
if ($saldo_antigo < $saldo) {
    $valor_difereca = $saldo - $saldo_antigo;
    $valor_difereca = number_format($valor_difereca, 2, ',', '.');
    $t->insereTransacao($id, "", $valor_difereca, "CREDITO PLATAFORMA", "CONCLUIDO");
} else if ($saldo_antigo > $saldo) {
    $valor_difereca = $saldo_antigo - $saldo;
    $valor_difereca = number_format($valor_difereca, 2, ',', '.');
    $t->insereTransacao($id, "", $valor_difereca, "DEBITO PLATAFORMA", "CONCLUIDO");
}

// Verifica se a conexão foi estabelecida
if ($conexao) {
    // Atualiza os dados do cliente, incluindo a senha (se houver)
    $query = "UPDATE clientes SET nome = ?, email = ?, telefone = ?, saldo = ?, senha = ? WHERE id = ?";
    $stmt = $conexao->prepare($query);

    if ($stmt) {
        $stmt->bind_param('sssssi', $nome, $email, $telefone, $saldo, $senha, $id);
        $cadastro = $stmt->execute();

        if ($cadastro) {
            echo "<script>alert('Cliente editado com sucesso!');window.location.href='listar_clientes.php'</script>";
        } else {
            echo "<script>alert('Erro ao editar cliente!');window.location.href='listar_clientes.php'</script>";
        }
    } else {
        echo "<script>alert('Erro na preparação da consulta.');window.location.href='listar_clientes.php'</script>";
    }
} else {
    echo "<script>alert('Erro na conexão com o banco de dados.');window.location.href='listar_clientes.php'</script>";
}
?>
