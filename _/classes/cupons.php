<?php
Class cupons {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastra_cupon($cidade_id, $nome, $valor, $valor_min, $primeira_compra, $validade, $quantidade, $uso_unico, $tipo_desconto) {
        try {
            $sql = "INSERT INTO cupons (cidade_id, nome, valor, valor_min, primeira_compra, validade, quantidade, uso_unico, tipo_desconto) VALUES (:cidade_id, :nome, :valor, :valor_min, :primeira_compra, :validade, :quantidade, :uso_unico, :tipo_desconto)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':cidade_id', $cidade_id);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':valor', $valor);
            $stmt->bindValue(':valor_min', $valor_min);
            $stmt->bindValue(':primeira_compra', $primeira_compra);
            $stmt->bindValue(':validade', $validade);
            $stmt->bindValue(':quantidade', $quantidade);
            $stmt->bindValue(':uso_unico', $uso_unico);
            $stmt->bindValue(':tipo_desconto', $tipo_desconto);
            $stmt->execute();
            $lastId = $this->conexao->lastInsertId();
            if ($lastId == 0) {
                return "Erro ao cadastrar cupom: Nenhum ID retornado.";
            }
            return $lastId;
        } catch (PDOException $e) {
            return "Erro ao cadastrar cupom: " . $e->getMessage();
        }
    }

    public function edit_cupon($id, $nome, $valor, $valor_min, $primeira_compra, $validade, $quantidade, $uso_unico, $tipo_desconto) {
        $sql = "UPDATE cupons SET nome = :nome, valor = :valor, valor_min = :valor_min, primeira_compra = :primeira_compra, validade = :validade, quantidade = :quantidade, uso_unico = :uso_unico, tipo_desconto = :tipo_desconto WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':valor_min', $valor_min);
        $stmt->bindValue(':primeira_compra', $primeira_compra);
        $stmt->bindValue(':validade', $validade);
        $stmt->bindValue(':quantidade', $quantidade);
        $stmt->bindValue(':uso_unico', $uso_unico);
        $stmt->bindValue(':tipo_desconto', $tipo_desconto);
        $stmt->execute();
    }

    public function get_cupons_cidade_id($cidade_id){
        $sql = "SELECT * FROM cupons WHERE cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    public function get_cupon($cupon_id){
        $sql = "SELECT * FROM cupons WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id", $cupon_id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    public function get_cupon_nome($nome, $cidade_id){
        $sql = "SELECT * FROM cupons WHERE nome = :nome AND cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":nome", $nome);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }

    public function diminui_quantidade($cupon_id){
        $sql = "UPDATE cupons SET quantidade = quantidade - 1 WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id", $cupon_id);
        $stmt->execute();
    }

    public function add_cupon_used($cidade_id, $nome, $user_id){
        $datetime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO cupons_usados (cidade_id, nome, user_id, date) VALUES (:cidade_id, :nome, :user_id, :data)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':cidade_id', $cidade_id);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':data', $datetime);
        $stmt->execute();
    }

    public function verifica_cupon_used($cidade_id, $nome, $user_id){
        $sql = "SELECT * FROM cupons_usados WHERE cidade_id = :cidade_id AND nome = :nome AND user_id = :user_id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->bindValue(":nome", $nome);
        $stmt->bindValue(":user_id", $user_id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function verifica_uso_loja($cidade_id, $user_id){
        $sql = "SELECT * FROM cupons_usados WHERE cidade_id = :cidade_id AND user_id = :user_id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->bindValue(":user_id", $user_id);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

}

?>