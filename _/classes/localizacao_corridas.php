<?php
Class localizacao_corridas {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insereLocation($corrida_id, $latitude, $longitude){
        $sql = $this->conexao->prepare("INSERT INTO localizacao_corridas (corrida_id, latitude, longitude) VALUES (:corrida_id, :latitude, :longitude)");
        $sql->bindValue(":corrida_id", $corrida_id);
        $sql->bindValue(":latitude", $latitude);
        $sql->bindValue(":longitude", $longitude);
        $sql->execute();
        //retorna o id do usuário cadastrado
        return $this->conexao->lastInsertId();
    
    }

    public function getByCorridaId($corrida_id){
        $sql = "SELECT latitude, longitude FROM localizacao_corridas WHERE corrida_id = :corrida_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":corrida_id", $corrida_id);
        $sql->execute();
        $dados = $sql->fetchAll();
        return $dados;
    }


}