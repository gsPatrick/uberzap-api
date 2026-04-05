<?php
include("../bd/config.php");
include("../classes/seguranca.php");

$secret_key = $_POST['secret'];
$s = new seguranca();

if ($s->compare_secret($secret_key)) {
    include("../bd/conexao.php");
    $id_motorista = $_POST['id_motorista'];
    $id_corrida = $_POST['id_corrida'];

    if (!$id_motorista || !$id_corrida) {
        echo "error_params";
        exit;
    }

    try {
        $sql = "INSERT INTO corridas_rejeitadas (id_motorista, id_corrida) VALUES (:id_motorista, :id_corrida)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_motorista' => $id_motorista, 'id_corrida' => $id_corrida]);
        echo "ok";
    } catch (Exception $e) {
        // Se já existir (duplicado), apenas ignora e retorna ok
        echo "ok";
    }
} else {
    echo "no_auth";
}
?>
