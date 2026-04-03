<?php
Class cobranca {
    private $pdo; 
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }
    public function cadastra_saldo($id_franqueado, $saldo, $valor_desconto){
        $tipo_cobranca = 1;
        $query = "INSERT INTO cobranca (id_franqueado, tipo_cobranca, saldo, valor_desconto) VALUES (:id_franqueado, :tipo_cobranca, :saldo, :valor_desconto)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_franqueado', $id_franqueado);
        $stmt->bindParam(':tipo_cobranca', $tipo_cobranca);
        $stmt->bindParam(':saldo', $saldo);
        $stmt->bindParam(':valor_desconto', $valor_desconto);
        $stmt->execute();
        return $stmt;
    }

    public function cadastra_porcentagem($id_franqueado, $porcentagem){
        $tipo_cobranca = 2;
        $query = "INSERT INTO cobranca (id_franqueado, tipo_cobranca, porcentagem) VALUES (:id_franqueado, :tipo_cobranca, :porcentagem)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_franqueado', $id_franqueado);
        $stmt->bindParam(':tipo_cobranca', $tipo_cobranca);
        $stmt->bindParam(':porcentagem', $porcentagem);
        $stmt->execute();
        return $stmt;
    }

    public function cadastra_mensal($id_franqueado, $valor_mensal){
        $tipo_cobranca = 3;
        $query = "INSERT INTO cobranca (id_franqueado, tipo_cobranca, valor_mensal) VALUES (:id_franqueado, :tipo_cobranca, :valor_mensal)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_franqueado', $id_franqueado);
        $stmt->bindParam(':tipo_cobranca', $tipo_cobranca);
        $stmt->bindParam(':valor_mensal', $valor_mensal);
        $stmt->execute();
        return $stmt;
    }

    public function get_cobranca_loja($id_franqueado){
        $query = "SELECT * FROM cobranca WHERE id_franqueado = :id_franqueado";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_franqueado', $id_franqueado);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }

    public function edita_cobranca_loja($id_franqueado, $tipo_cobranca, $saldo, $valor_desconto, $porcentagem, $valor_mensal){
        $query = "UPDATE cobranca SET tipo_cobranca = :tipo_cobranca, saldo = :saldo, valor_desconto = :valor_desconto, porcentagem = :porcentagem, valor_mensal = :valor_mensal WHERE id_franqueado = :id_franqueado";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_franqueado', $id_franqueado);
        $stmt->bindParam(':tipo_cobranca', $tipo_cobranca);
        $stmt->bindParam(':saldo', $saldo);
        $stmt->bindParam(':valor_desconto', $valor_desconto);
        $stmt->bindParam(':porcentagem', $porcentagem);
        $stmt->bindParam(':valor_mensal', $valor_mensal);
        $stmt->execute();
        return $stmt;
    }

}

?>