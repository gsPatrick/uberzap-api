<?php
Class motoristas {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastrar_motorista($cidade_id, $nome, $email, $cpf, $img, $veiculo, $placa, $telefone, $senha, $taxa, $saldo, $ids_categorias){
        $sql = $this->conexao->prepare("INSERT INTO motoristas (cidade_id, nome, email, cpf, img, veiculo, placa, telefone, senha, taxa, saldo, ids_categorias) VALUES (:cidade_id, :nome, :email, :cpf, :img, :veiculo, :placa, :telefone, :senha, :taxa, :saldo, :ids_categorias)");
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":email", $email);
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":img", $img);
        $sql->bindValue(":veiculo", $veiculo);
        $sql->bindValue(":placa", $placa);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":taxa", $taxa);
        $sql->bindValue(":saldo", $saldo);
        $ids_categorias = json_encode($ids_categorias);
        $sql->bindValue(":ids_categorias", $ids_categorias);
        $sql->execute();
        return $this->conexao->lastInsertId();
    }

    public function edit_motorista($id, $nome, $email, $cpf, $img, $veiculo, $placa, $telefone, $senha, $taxa, $saldo, $ids_categorias){
        $sql = $this->conexao->prepare("UPDATE motoristas SET nome = :nome, email = :email, cpf = :cpf, img = :img, veiculo = :veiculo, placa = :placa, telefone = :telefone, senha = :senha, taxa = :taxa, saldo = :saldo, ids_categorias = :ids_categorias WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":email", $email);
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":img", $img);
        $sql->bindValue(":veiculo", $veiculo);
        $sql->bindValue(":placa", $placa);
        $sql->bindValue(":telefone", $telefone);
        $sql->bindValue(":senha", $senha);
        $sql->bindValue(":taxa", $taxa);
        $sql->bindValue(":saldo", $saldo);
        $ids_categorias = json_encode($ids_categorias);
        $sql->bindValue(":ids_categorias", $ids_categorias);
        $sql->execute();
    }

    public function get_motoristas($cidade_id, $ativo = true){
        if($ativo){
            $sql = "SELECT * FROM motoristas WHERE cidade_id = :cidade_id AND ativo = 1";
        } else {
            $sql = "SELECT * FROM motoristas WHERE cidade_id = :cidade_id AND ativo = 2";
        }
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $dados = $sql->fetchAll();
        return $dados;
    }

    public function get_all_motoristas(){
        $sql = $this->conexao->prepare("SELECT * FROM motoristas");
        $sql->execute();
        $dados = $sql->fetchAll();
        if($dados){
            return $dados;
        } else {
            return false;
        }
    }

    public function get_motorista($id){
        $sql = $this->conexao->prepare("SELECT * FROM motoristas WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        $dados = $sql->fetch();
        if($dados){
            return $dados;
        } else {
            return array(
                "id" => 0,
                "nome" => "Não Atribuído",
                "ids_categorias" => "[]"
            );
        }
    }

    

    public  function login_motorista($cpf, $senha){
        $sql = $this->conexao->prepare("SELECT * FROM motoristas WHERE cpf = :cpf AND senha = :senha AND ativo =  '1'");
        $sql->bindValue(":cpf", $cpf);
        $sql->bindValue(":senha", $senha);
        $sql->execute();
        $dados = $sql->fetch();
        if($dados){
            return $dados;
        } else {
            return false;
        }
    }

    public function atualiza_coordenadas($id, $lat, $lng){
        $sql = $this->conexao->prepare("UPDATE motoristas SET latitude = :lat, longitude = :lng WHERE id = :id");
        $sql->bindValue(":lat", $lat);
        $sql->bindValue(":lng", $lng);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function atualiza_disponibilidade($id, $online){
        $sql = $this->conexao->prepare("UPDATE motoristas SET online = :online WHERE id = :id");
        $sql->bindValue(":online", $online);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function get_motorista_cpf($cpf){
        $sql = $this->conexao->prepare("SELECT * FROM motoristas WHERE cpf = :cpf");
        $sql->bindValue(":cpf", $cpf);
        $sql->execute();
        $dados = $sql->fetch();
        if($dados){
            return $dados;
        } else {
            return false;
        }
    }

    public function verifica_se_esta_ativo($id){
        $sql = $this->conexao->prepare("SELECT * FROM motoristas WHERE id = :id AND ativo = 1");
        $sql->bindValue(":id", $id);
        $sql->execute();
        $dados = $sql->fetch();
        if($dados){
            return true;
        } else {
            return false;
        }
    }

    public function atualiza_saldo($id, $saldo){ 
        $sql = $this->conexao->prepare("UPDATE motoristas SET saldo = :saldo WHERE id = :id");
        $sql->bindValue(":saldo", $saldo);
        $sql->bindValue(":id", $id);
        $sql->execute();
    }

    public function get_taxa_motorista($id){
        $sql = $this->conexao->prepare("SELECT taxa FROM motoristas WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        $dados = $sql->fetch();
        if($dados){
            return $dados["taxa"];
        } else {
            return false;
        }
    }

    public function get_valor_taxa($valor_corrida, $motorita_id){
        $valor_corrida = str_replace(",", ".", $valor_corrida);
        $taxa = $this->get_taxa_motorista($motorita_id);
        $valor_taxa = ($valor_corrida * $taxa) / 100;
        return $valor_taxa;
    }

}

?>