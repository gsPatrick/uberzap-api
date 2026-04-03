<?php
Class configuracoes_pagamento {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function create_configuracoes_pagamento($cidade_id, $token){
        $sql = "INSERT INTO configuracoes_pagamento (cidade_id, token) VALUES (:cidade_id, :token)";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":token", $token);
        $sql->execute();
    }

    public function read_configuracoes_pagamento($cidade_id){
        $sql = "SELECT * FROM configuracoes_pagamento WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        if($sql->rowCount() > 0){
            $dados = $sql->fetch();
            return $dados;
        } else {
            return array(
                'token' => ''
            );
        }
    }

    public function update_configuracoes_pagamento($cidade_id, $token){
        $sql = "UPDATE configuracoes_pagamento SET token = :token WHERE cidade_id = :cidade_id";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":token", $token);
        $sql->execute();
    }

}

?>