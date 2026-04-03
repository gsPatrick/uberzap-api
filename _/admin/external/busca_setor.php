<?php
$cidade_id = $_POST['id'];
include $_SERVER['DOCUMENT_ROOT'] . "/_/bd/conexao.php";
$busca = mysqli_query($conexao, "SELECT id, nome FROM setores WHERE cidade_id = '$cidade_id'");
while ($linha = mysqli_fetch_assoc($busca)) {
    $setor_[] = $linha;
}
echo json_encode($setor_);
?>