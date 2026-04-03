<?php
Class avaliacoes {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insere($cliente_id, $motorista_id, $nota, $comentario, $corrida_id){
        $query = "INSERT INTO avaliacoes (cliente_id, motorista_id, nota, comentario, corrida_id) VALUES (:cliente_id, :motorista_id, :nota, :comentario, :corrida_id)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->bindParam(':nota', $nota);
        $stmt->bindParam(':comentario', $comentario);
        $stmt->bindParam(':corrida_id', $corrida_id);
        $stmt->execute();
        return $this->conexao->lastInsertId();
    }

    public function get_avaliacoes_motorista($motorista_id){
        $query = "SELECT * FROM avaliacoes WHERE motorista_id = :motorista_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function get_avaliacao($corrida_id){
        $query = "SELECT * FROM avaliacoes WHERE corrida_id = :corrida_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':corrida_id', $corrida_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($avaliacao = $stmt->fetch()){
            return $avaliacao;
        }else{
            return false;
        }
    }

    public function verifica_avaliacao($corrida_id){
        $query = "SELECT * FROM avaliacoes WHERE corrida_id = :corrida_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':corrida_id', $corrida_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->fetch()){
            return true;
        }else{
            return false;
        }
    }

    public function get_media_avaliacoes($motorista_id){
        $query = "SELECT AVG(nota) as media FROM avaliacoes WHERE motorista_id = :motorista_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($media = $stmt->fetch()){
            return $media['media'];
        }else{
            return 0;
        }
    }

}


?>