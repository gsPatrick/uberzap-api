<?php
Class admin {
    private $pdo;
    public function __construct() {
        include '../bd/conexao.php';
        $this->pdo = $pdo;
    }

    public function cadastra_admin($user, $senha, $cidade_id, $telefone){
        $sql = "INSERT INTO admin (user, senha, cidade_id, telefone, admin) VALUES (:user, :senha, :cidade_id, :telefone, 0)";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":user", $user);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":telefone", $telefone);
        $sql->execute();
    }

    public function login($user, $senha) {
        $query = $this->pdo->prepare("SELECT * FROM admin WHERE user = ? AND senha = ?");
        $query->bindValue(1, $user);
        $query->bindValue(2, $senha);
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            return true;
        } else {
            return false;
        }
    }

    public function get_user_id($user) {
        $query = $this->pdo->prepare("SELECT * FROM admin WHERE user = ?");
        $query->bindValue(1, $user);
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            $dados = $query->fetch();
            return $dados['id'];
        } else {
            return false;
        }
    }

    public function get_cidade_id($user_id){
        $query = $this->pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $query->bindValue(1, $user_id);
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            $dados = $query->fetch();
            return $dados['cidade_id'];
        } else {
            return false;
        }
    }

    public function get_admin($user_id){
        $query = $this->pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $query->bindValue(1, $user_id);
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            $dados = $query->fetch();
            return $dados['admin'];
        } else {
            return "0";
        }
    }

    public function get_all_admins(){
        $query = $this->pdo->prepare("SELECT * FROM admin");
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            $dados = $query->fetchAll();
            return $dados;
        } else {
            return false;
        }
    }

    public function get_dados_admin($id){
        $query = $this->pdo->prepare("SELECT * FROM admin WHERE id = ?");
        $query->bindValue(1, $id);
        $query->execute();
        $cont = $query->rowCount();
        if($cont>0){
            $dados = $query->fetch();
            return $dados;
        } else {
            return false;
        }
    }

    public function edit_admin($id, $user, $senha, $cidade_id, $telefone){
        $sql = "UPDATE admin SET user = :user, senha = :senha, cidade_id = :cidade_id, telefone = :telefone WHERE id = :id";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":user", $user);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function delete_admin($id){
        $sql = "DELETE FROM admin WHERE id = :id";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

}

?>