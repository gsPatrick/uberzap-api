<?php
include("../bd/config.php");
include("../classes/motoristas.php");
include("../classes/seguranca.php");

$secret_key = $_POST['secret'];
$s = new seguranca();

if ($s->compare_secret($secret_key)) {
    include("../bd/conexao.php");
    $id_motorista = $_POST['id_motorista'];
    if (!$id_motorista) {
        echo "no";
        exit;
    }
    
    $m = new motoristas();
    $dados = $m->get_motorista($id_motorista);
    
    if ($dados) {
        // Garante que campos numéricos sejam strings para o JSON se necessário
        $dados['saldo'] = str_replace('.', ',', $dados['saldo']); 
        echo json_encode($dados);
    } else {
        echo "no";
    }
} else {
    echo "no_auth";
}
?>
