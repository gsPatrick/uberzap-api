<?php
class transacoes_motoristas
{
    private $cidade_id;
    private $conexao;
    public function __construct($cidade_id)
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
        $this->cidade_id = $cidade_id;
    }

    public function insereTransacao($user_id, $ref, $valor, $metodo, $status, $link = ""){
        if($ref == ""){
            $ref = "N/A";
        }
        $sql = "INSERT INTO transacoes_motoristas (cidade_id, user_id, ref, valor, metodo, status, link) VALUES (:cidade_id, :user_id, :ref, :valor, :metodo, :status, :link)";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $this->cidade_id);
        $sql->bindValue(":user_id", $user_id);
        $sql->bindValue(":ref", $ref);
        $sql->bindValue(":valor", $valor);
        $sql->bindValue(":metodo", $metodo);
        $sql->bindValue(":status", $status);
        $sql->bindValue(":link", $link);
        $sql->execute();
        return true;
    }

    public function atualizaStatusId($id, $status){
        $sql = "UPDATE transacoes_motoristas SET status = :status WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":status", $status);
        $sql->bindValue(":id", $id);
        $sql->execute();
        return true;
    }

    public function getById($id){
        $sql = "SELECT * FROM transacoes_motoristas WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        if($sql->rowCount() > 0){
            return $sql->fetch();
        }else{
            return false;
        }
    }

    public function getByUserId($user_id){
        $sql = "SELECT * FROM transacoes_motoristas WHERE user_id = :user_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":user_id", $user_id);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        if($sql->rowCount() > 0){
            return $sql->fetchAll();
        }else{
            return false;
        }
    }

    public function getByCidadeId($cidade_id){
        $sql = "SELECT * FROM transacoes_motoristas WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        if($sql->rowCount() > 0){
            return $sql->fetchAll();
        }else{
            return false;
        }
    }
    
}