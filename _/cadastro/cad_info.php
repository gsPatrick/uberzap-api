<?php
include("../classes/motorista_docs.php");
include("../classes/upload.php");
$md = new motorista_docs();
//entrada de formulário

$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];
$senha = $_POST['senha'];
$telefone = $_POST['telefone'];
$cidade_id = $_POST['cidade_id'];
$veiculo = $_POST['veiculo'];
$placa = $_POST['placa'];


if ($md->verifica_cpf($cpf)) {
    echo "<script>alert('CPF já cadastrado!'); window.location.href = 'index.php';</script>";
}
else {

    //parametros para imagem

    $pasta = '../admin/uploads/';

    $img_cnh = $_FILES['img_cnh'];
    $img_documento = $_FILES['img_documento'];
    $img_lateral = $_FILES['img_lateral'];
    $img_frente = $_FILES['img_frente'];
    $img_selfie = $_FILES['img_selfie'];
    $img_antecedentes = $_FILES['img_antecedentes'];

    $imagens = array(
        $img_cnh, $img_documento, $img_lateral, $img_frente, $img_selfie, $img_antecedentes
    );
    $imagens_fim = array();
    if (is_dir($pasta)) {
        for ($index = 0; $index < count($imagens); $index++) {
            $upload = new Upload($imagens[$index], 800, 800, $pasta);
            $imagens_fim[] = $upload->salvar();
        }
    }

    //echo json_encode($imagens_fim);
    $md->insert($cidade_id, $nome, $cpf, $senha, $telefone, $veiculo, $placa, $imagens_fim[0], $imagens_fim[1], $imagens_fim[2], $imagens_fim[3], $imagens_fim[4], $imagens_fim[5], $email);

    echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = 'index.php';</script>";
}


mysqli_close($conexao);

?>