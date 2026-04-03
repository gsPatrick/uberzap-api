<?php
Class cidades {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastra($nome, $latitude = "", $longitude = "") {
        $sql = "INSERT INTO cidades (nome, latitude, longitude) VALUES (:nome, :latitude, :longitude)";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":latitude", $latitude);
        $sql->bindValue(":longitude", $longitude);
        $sql->execute();
        return true;
    }

    public function editar($id, $nome, $latitude, $longitude) {
        $sql = "UPDATE cidades SET nome = :nome, latitude = :latitude, longitude = :longitude WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":latitude", $latitude);
        $sql->bindValue(":longitude", $longitude);
        $sql->execute();
        return true;
    }

    public function get_cidades() {
        $query = "SELECT * FROM cidades";
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function get_cidade($id) {
        $query = "SELECT nome FROM cidades WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $dados = $stmt->fetch();
        return $dados['nome'];
    }

    public function get_dados_cidade($id) {
        $query = "SELECT * FROM cidades WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $dados = $stmt->fetch();
        if($dados){
            return $dados;
        }else{
            return false;
        }
    }

    public function get_cidades_id($cidade_id){
        $query = "SELECT * FROM cidades WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":id", $cidade_id);
        $stmt->execute();
        return $stmt;
    }

    public function get_array_cidades(){
        $query = "SELECT * FROM cidades";
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        $dados = $stmt->fetchAll();
        return $dados;
    }

    public function getByIndex($index) //percorre as cidades e busca o index - 1
    {
        $query = "SELECT * FROM cidades";
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        $dados = $stmt->fetchAll();
        return $dados[$index - 1];
    }

}

?>