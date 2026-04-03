<?php
Class franqueados {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }
    public function cadastra_usuario($nome, $usuario, $senha, $cidade_id, $comissao, $telefone, $email) {
        $sql = $this->conexao->prepare("INSERT INTO franqueados (nome, usuario, senha, cidade_id, comissao, telefone, email) VALUES (:nome, :usuario, :senha, :cidade_id, :comissao, :telefone, :email)");
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":usuario", $usuario);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":comissao", $comissao);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":email", $email);
        $sql->execute();
        //retorna o id do usuário cadastrado
        return $this->conexao->lastInsertId();
    }

    public function edit_usuario($id, $nome, $usuario, $senha, $cidade_id, $comissao, $telefone, $email){
        $sql = "UPDATE franqueados SET nome = :nome, usuario = :usuario, senha = :senha, cidade_id = :cidade_id, comissao = :comissao, telefone = :telefone, email = :email WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":usuario", $usuario);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":comissao", $comissao);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":email", $email);
        $sql->execute();
    }


    public function get_user_id($usuario){
        $sql = "SELECT id FROM franqueados WHERE usuario = :usuario";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":usuario", $usuario);
        $sql->execute();
        $dados = $sql->fetch();
        return $dados['id'];
    }

    public function get_usuarios_cidade($cidade_id){
        $sql = "SELECT * FROM franqueados WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $dados = $sql->fetchAll();
        return $dados;
    }

    public function get_usuarios_loja($loja_id){
        $sql = "SELECT * FROM franqueados WHERE loja_id = :loja_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":loja_id", $loja_id);
        $sql->execute();
        $dados = $sql->fetchAll();
        return $dados;
    }

    

    public function delet_usuario($id){
        $sql = "DELETE FROM franqueados WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function get_usuario_id($id){
        $sql = "SELECT * FROM franqueados WHERE id = :id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
        $dados = $sql->fetch();
        return $dados;
    }

    public function login($usuario, $senha){
        $sql = "SELECT * FROM franqueados WHERE usuario = :usuario AND senha = :senha";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":usuario", $usuario);
        $sql->bindValue(":senha", $senha);
        $sql->execute();
        $dados = $sql->fetch();
        return $dados;
    }
}

?>