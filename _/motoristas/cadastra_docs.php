<?php
header("access-control-allow-origin: *");
include("../bd/config.php");
include("../classes/motorista_docs.php");
include("../classes/upload.php");
include("../classes/seguranca.php");

$s = new seguranca();
$secret_key = $_POST['secret'];

if ($s->compare_secret($secret_key)) {
    $md = new motorista_docs();
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'];
    $cidade_id = $_POST['cidade_id'];
    $veiculo = $_POST['veiculo'];
    $placa = $_POST['placa'];

    if ($md->verifica_cpf($cpf)) {
        echo json_encode(["status" => "error", "message" => "CPF já cadastrado!"]);
    } else {
        $pasta = '../admin/uploads/';
        
        // Mapeamento das imagens esperadas
        $chaves_imagens = [
            'img_cnh', 'img_documento', 'img_lateral', 
            'img_frente', 'img_selfie', 'img_antecedentes'
        ];
        
        $imagens_fim = [];
        
        foreach ($chaves_imagens as $chave) {
            if (isset($_FILES[$chave])) {
                $upload = new Upload($_FILES[$chave], 800, 800, $pasta);
                $imagens_fim[] = $upload->salvar();
            } else {
                $imagens_fim[] = ""; // Caso falte alguma imagem opcional ou erro
            }
        }

        // Insere no banco de dados de documentos pendentes
        $res = $md->insert(
            $cidade_id, $nome, $cpf, $senha, $telefone, 
            $veiculo, $placa, 
            $imagens_fim[0], $imagens_fim[1], $imagens_fim[2], 
            $imagens_fim[3], $imagens_fim[4], $imagens_fim[5], 
            $email
        );

        if ($res) {
            echo json_encode(["status" => "ok", "message" => "Documentos enviados com sucesso! Aguarde a aprovação."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao salvar documentos."]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Falha de autenticação."]);
}
?>
