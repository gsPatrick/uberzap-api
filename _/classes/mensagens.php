<?php
Class mensagens {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }
    //tabela msg
 

    public function insere_msg($id_corrida, $msg, $sender){
        $query = "INSERT INTO msg (id_corrida, msg, sender) VALUES (:id_corrida, :msg, :sender)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_corrida', $id_corrida);
        $stmt->bindParam(':msg', $msg);
        $stmt->bindParam(':sender', $sender);
        $stmt->execute();
        return $this->conexao->lastInsertId();
    }

    public function get_all_msg($id_corrida){
        $query = "SELECT * FROM msg WHERE id_corrida = :id_corrida ORDER BY id ASC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_corrida', $id_corrida);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }

    public function get_last($id_corrida){
        $query = "SELECT * FROM msg WHERE id_corrida = :id_corrida ORDER BY id DESC LIMIT 1";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_corrida', $id_corrida);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            return $stmt->fetch();
        }else{
            return false;
        }
    }

}