<?php
class motorista_docs
{
    private $pdo;
    private $conexao;
    public function __construct()
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    // estrutura: `cidade_id`, `nome`, `cpf`, `senha`, `telefone`, `veiculo`, `placa`, `img_cnh`, `img_documento`, `img_lateral`, `img_frente`, `img_selfie`

    public function insert($cidade_id, $nome, $cpf, $senha, $telefone, $veiculo, $placa, $img_cnh, $img_documento, $img_lateral, $img_frente, $img_selfie, $img_antecedentes, $email)
    {
        $query = "INSERT INTO motorista_docs (cidade_id, nome, cpf, senha, telefone, veiculo, placa, img_cnh, img_documento, img_lateral, img_frente, img_selfie, img_antecedentes, email) VALUES (:cidade_id, :nome, :cpf, :senha, :telefone, :veiculo, :placa, :img_cnh, :img_documento, :img_lateral, :img_frente, :img_selfie, :img_antecedentes, :email)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':veiculo', $veiculo);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':img_cnh', $img_cnh);
        $stmt->bindParam(':img_documento', $img_documento);
        $stmt->bindParam(':img_lateral', $img_lateral);
        $stmt->bindParam(':img_frente', $img_frente);
        $stmt->bindParam(':img_selfie', $img_selfie);
        $stmt->bindParam(':img_antecedentes', $img_antecedentes);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt;
    }

    public function get_by_cidade($cidade_id)
    {
        $query = "SELECT * FROM motorista_docs WHERE cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll();
        }
        else {
            return false;
        }
    }

    public function get_by_id($id)
    {
        $query = "SELECT * FROM motorista_docs WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        else {
            return false;
        }
    }

    public function verifica_cpf($cpf)
    {
        $query = "SELECT * FROM motorista_docs WHERE cpf = :cpf";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

}
?>