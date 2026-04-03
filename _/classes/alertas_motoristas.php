<?php
Class alertas_motoristas {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insere($id_motorista, $msg, $executado = 0) {
        $query = "INSERT INTO alertas_motoristas (id_motorista, msg, executado) VALUES (:id_motorista, :msg, :executado)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_motorista', $id_motorista);
        $stmt->bindParam(':msg', $msg);
        $stmt->bindParam(':executado', $executado);
        $stmt->execute();
        return $this->conexao->lastInsertId();
    }

    public function getByMotorista($id_motorista) {
        $query = "SELECT * FROM alertas_motoristas WHERE id_motorista = :id_motorista AND executado = 0";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id_motorista', $id_motorista);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function setExecutado($id_alerta) {
        $query = "UPDATE alertas_motoristas SET executado = 1 WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id_alerta);
        $stmt->execute();
        return $stmt->rowCount();
    }

}