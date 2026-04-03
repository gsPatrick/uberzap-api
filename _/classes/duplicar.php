<?php
Class duplicar {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }
    //function to duplicate a row wite id ++
    public function duplicar($id, $categoria) {
        $is_id = false;
        $sql = "SELECT * FROM $categoria WHERE id = $id";
        $sql = $this->conexao->prepare($sql);
        $sql->execute();
        $dados = $sql->fetch(PDO::FETCH_ASSOC);
        $sql = "INSERT INTO $categoria (";
        foreach ($dados as $key => $value) {
            if($key != 'id'){
                $sql .= $key . ',';
                $is_id = false;
            }else{
                $is_id = true;
            }
        }
        $sql = substr($sql, 0, -1);
        $sql .= ") VALUES (";
        foreach ($dados as $key => $value) {
            if($key != 'id'){
                 $sql .= "'" . $value . "',";
            }
        }
        $sql = substr($sql, 0, -1);
        $sql .= ")";
        $sql = $this->conexao->prepare($sql);
        $sql->execute();
    }
}


?>