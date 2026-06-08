<?php
Class motoristas {
    private $pdo;
    private $conexao;
    public function __construct() {
        require_once __DIR__ . '/../bd/conexao.php';
        global $pdo;
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

    public function edit_motorista($id, $nome, $email, $cpf, $img, $veiculo, $placa, $telefone, $senha, $taxa, $saldo, $ids_categorias) {
    // Prepare a consulta SQL para atualizar os dados do motorista
    $sql = $this->conexao->prepare("
        UPDATE motoristas 
        SET 
            nome = :nome, 
            email = :email, 
            cpf = :cpf, 
            img = :img, 
            veiculo = :veiculo, 
            placa = :placa, 
            telefone = :telefone, 
            senha = :senha, 
            taxa = :taxa, 
            saldo = :saldo, 
            ids_categorias = :ids_categorias 
        WHERE id = :id
    ");
    
    // Vincula os valores aos parâmetros da consulta
    $sql->bindValue(":id", $id);
    $sql->bindValue(":nome", $nome);
    $sql->bindValue(":cpf", $cpf);
    $sql->bindValue(":img", $img);
    $sql->bindValue(":veiculo", $veiculo);
    $sql->bindValue(":placa", $placa);
    $sql->bindValue(":telefone", $telefone);
    $sql->bindValue(":senha", $senha);
    $sql->bindValue(":taxa", $taxa);
    $sql->bindValue(":saldo", $saldo);
    $sql->bindValue(":email", $email);
    
    // Codifica o array de ids_categorias em formato JSON
    $ids_categorias = json_encode($ids_categorias);
    $sql->bindValue(":ids_categorias", $ids_categorias);

    // Executa a consulta
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

    // Motoristas ONLINE da cidade com coordenadas válidas (para o radar de carrinhos no mapa).
    public function get_motoristas_proximos($cidade_id){
        $sql = $this->conexao->prepare(
            "SELECT id, latitude, longitude, veiculo, placa, ids_categorias, online, ativo
             FROM motoristas
             WHERE cidade_id = :cid
               AND ativo = '1'
               AND online <> 0
               AND latitude IS NOT NULL AND latitude <> '' AND latitude <> '0'
               AND longitude IS NOT NULL AND longitude <> '' AND longitude <> '0'"
        );
        $sql->bindValue(':cid', $cidade_id);
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $dados ?: array();
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
        require_once __DIR__ . '/../bd/normalize.php';
        $digits = ubezap_digits_only($cpf);
        if ($digits === '') {
            return false;
        }

        $cpfExpr = ubezap_sql_digits_expr('cpf');
        $telExpr = ubezap_sql_digits_expr('telefone');

        $sql = $this->conexao->prepare(
            "SELECT * FROM motoristas
             WHERE senha = :senha
               AND ativo = '1'
               AND (
                    $cpfExpr = :digits_cpf
                    OR $telExpr = :digits_tel
               )
             LIMIT 1"
        );
        $sql->bindValue(':digits_cpf', $digits);
        $sql->bindValue(':digits_tel', $digits);
        $sql->bindValue(':senha', $senha);
        $sql->execute();
        $dados = $sql->fetch();
        if ($dados) {
            return $dados;
        }
        return false;
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

    /** Motoristas online e disponíveis para receber corrida (online = 1, não em viagem). */
    public function get_motoristas_online_disponiveis($cidade_id)
    {
        $sql = $this->conexao->prepare(
            "SELECT * FROM motoristas WHERE cidade_id = :cidade_id AND ativo = '1' AND online = '1'"
        );
        $sql->bindValue(':cidade_id', $cidade_id);
        $sql->execute();
        $dados = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $dados ?: [];
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

    public function atualiza_push_token($id, $token)
    {
        $sql = $this->conexao->prepare("UPDATE motoristas SET id_signal = :token WHERE id = :id");
        $sql->bindValue(':token', (string) $token);
        $sql->bindValue(':id', (int) $id);
        return $sql->execute();
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