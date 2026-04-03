<?php
Class Taximetro { 
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo; 
    }

    public function insere($cidade_id, $tx_minima, $tx_minuto, $tx_km) {
        $cmd = "SELECT * FROM taximetro WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $taximetro = $sql->fetch(PDO::FETCH_ASSOC);
        if($taximetro){
            $this->update($cidade_id, $tx_minima, $tx_minuto, $tx_km);
            return true;
        }else{
            $cmd = "INSERT INTO taximetro (cidade_id, tx_minima, tx_minuto, tx_km) VALUES (:cidade_id, :tx_minima, :tx_minuto, :tx_km)";
            $sql = $this->conexao->prepare($cmd);
            $sql->bindValue(":cidade_id", $cidade_id);
            $sql->bindValue(":tx_minima", $tx_minima);
            $sql->bindValue(":tx_minuto", $tx_minuto);
            $sql->bindValue(":tx_km", $tx_km);
            $sql->execute();
            return true;
        }
    
    }

    private function update($cidade_id, $tx_minima, $tx_minuto, $tx_km) {
        $cmd = "UPDATE taximetro SET tx_minima = :tx_minima, tx_minuto = :tx_minuto, tx_km = :tx_km WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":tx_minima", $tx_minima);
        $sql->bindValue(":tx_minuto", $tx_minuto);
        $sql->bindValue(":tx_km", $tx_km);
        $sql->execute();
    }


    public function getByCidadeId($cidade_id){
        $cmd = "SELECT * FROM taximetro WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $taximetro = $sql->fetch(PDO::FETCH_ASSOC);
        return $taximetro;
    }

}
