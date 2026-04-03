<?php
Class otp_verifications {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insere($numero_telefone){
        $otp = rand(100000, 999999);
        $sql = "INSERT INTO otp_verifications (numero_telefone, otp) VALUES (:numero_telefone, :otp)";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":numero_telefone", $numero_telefone);
        $sql->bindValue(":otp", $otp);
        $sql->execute();
        return $otp;
    }

    public function verifica_otp($numero_telefone, $otp){
        $date = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        $query = "SELECT * FROM otp_verifications WHERE numero_telefone = :numero_telefone AND otp = :otp AND date > :date";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":numero_telefone", $numero_telefone);
        $stmt->bindValue(":otp", $otp);
        $stmt->bindValue(":date", $date);
        $stmt->execute();
        $dados = $stmt->fetch();
        if($dados){
            return true;
        }else{
            return false;
        }
    }

}