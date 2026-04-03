<?php
Class status_historico { 
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo; 
    }

    public function salva_status($pedido_id, $status, $origem = "Sistema", $local = ""){
        $hora = date('Y-m-d H:i:s');
        $status = "(".$origem.") " . $status;
        $cmd = "INSERT INTO status_historico (pedido_id, status, hora, local) VALUES (:pedido_id, :status, :hora, :local)";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":pedido_id", $pedido_id);
        $sql->bindValue(":status", $status);
        $sql->bindValue(":hora", $hora);
        $sql->bindValue(":local", $local);
        $sql->execute();
    }

    public function get_status($pedido_id){
        $cmd = "SELECT * FROM status_historico WHERE pedido_id = :pedido_id ORDER BY id ASC";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":pedido_id", $pedido_id);
        $sql->execute();
        $status = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $status;
    }

    public function get_ultimo_status($pedido_id){
        $cmd = "SELECT * FROM status_historico WHERE pedido_id = :pedido_id ORDER BY id DESC LIMIT 1";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":pedido_id", $pedido_id);
        $sql->execute();
        $status = $sql->fetch(PDO::FETCH_ASSOC);
        if($status){
            return $status;
        }else{
            return false;
        }
    }

}

?>