<?php
Class clientes {
    private $pdo; 
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }
    public function cadastra($cidade_id, $nome, $email, $telefone, $senha, $latitude = "0", $longitude = "0", $id_signal = "", $ativo = 1, $saldo = "0,00") {
        $query = "INSERT INTO clientes (cidade_id, nome, email, telefone, latitude, longitude, id_signal, ativo, saldo, senha) VALUES (:cidade_id, :nome, :email, :telefone, :latitude, :longitude, :id_signal, :ativo, :saldo, :senha)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':id_signal', $id_signal);
        $stmt->bindParam(':ativo', $ativo);
        $stmt->bindParam(':saldo', $saldo);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        return $stmt;
    }
    
    // essa parte do codigo foi fornecido pelo chatgpt
    public function edita($id, $nome, $email, $telefone, $saldo = "0,00", $senha = null) {
    $query = "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, saldo = :saldo";
    
    // Adiciona o campo de senha somente se estiver presente
    if (!is_null($senha)) {
        // Criptografar a senha antes de salvar
        $salt = "anjdsn5s141d5";
        $senha_criptografada = md5($senha.$salt);
        $query .= ", senha = :senha";
    }
    
    $query .= " WHERE id = :id";

    $stmt = $this->conexao->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':saldo', $saldo);

    // Bind da senha criptografada apenas se ela não for nula
    if (!is_null($senha)) {
        $stmt->bindParam(':senha', $senha_criptografada);
    }

    $stmt->execute();
    return $stmt;
}
// aqui termina o codigo enviado pelo chatgpt

    public function get_cliente_id($id){
        $query = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            return $stmt->fetch();
        }else{
            return false;
        }
    }

    public function verifica_se_existe($telefone){
        $query = "SELECT * FROM clientes WHERE telefone = :telefone";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            return $stmt->fetch();
        }else{
            return false;
        }
    }


    public function redefinir_senha($id, $senha){
        $query = "UPDATE clientes SET senha = :senha WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        return $stmt;
    }

    public function get_clientes_cidade(){
        $query = "SELECT * FROM clientes WHERE cidade_id = :cidade_id ORDER BY nome ASC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $_SESSION['cidade_id']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            $dados =  $stmt->fetchAll();
            //mostra ativos primeiro
            $ativos = array();
            $inativos = array();
            foreach ($dados as $key => $value) {
                if($value['ativo'] == 1){
                    $ativos[] = $value;
                }else{
                    $inativos[] = $value;
                }
            }
            $dados = array_merge($ativos, $inativos);
            return $dados;
        }else{
            return false;
        }
        
    }

    public function ativar_desativar($id, $ativo){
        $query = "UPDATE clientes SET ativo = :ativo WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ativo', $ativo);
        $stmt->execute();
        return $stmt;
    }

    public function login($telefone, $senha){
        $salt = "anjdsn5s141d5";
        $senha = md5($senha.$salt);
        $query = "SELECT * FROM clientes WHERE telefone = :telefone AND senha = :senha";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            return $stmt->fetch();
        }else{
            return false;
        }
    }

    public function atualiza_saldo($id, $saldo){
        $query = "UPDATE clientes SET saldo = :saldo WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':saldo', $saldo);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function get_cliente_telefone($telefone){
        $query = "SELECT * FROM clientes WHERE telefone = :telefone";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":telefone", $telefone);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt;
        }else{
            return false;
        } 
    }

    public function resetar_senha_telefone($telefone, $senha){
        $sql = "UPDATE clientes SET senha = :senha WHERE telefone = :telefone";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":senha", $senha);
        $sql->execute();
        return true;
    }





}