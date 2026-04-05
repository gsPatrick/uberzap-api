<?php
class corridas
{
    private $pdo;
    private $conexao;
    public function __construct()
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insere_corrida($motorista_id, $cliente_id, $cidade_id, $lat_ini, $lng_ini, $lat_fim, $lng_fim, $km, $tempo, $endereco_ini_txt, $endereco_fim_txt, $taxa, $f_pagamento, $status_pagamento, $ref_pagamento, $cupom, $categoria_id = 0, $nome_cliente = "")
    {
        $ref = uniqid();
        $query = "INSERT INTO corridas (ref, motorista_id, cliente_id, cidade_id, lat_ini, lng_ini, lat_fim, lng_fim, km, tempo, endereco_ini_txt, endereco_fim_txt, taxa, f_pagamento, status_pagamento, ref_pagamento, cupom, categoria_id, nome_cliente) VALUES (:ref, :motorista_id, :cliente_id, :cidade_id, :lat_ini, :lng_ini, :lat_fim, :lng_fim, :km, :tempo, :endereco_ini_txt, :endereco_fim_txt, :taxa, :f_pagamento, :status_pagamento, :ref_pagamento, :cupom, :categoria_id, :nome_cliente)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':ref', $ref);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->bindParam(':lat_ini', $lat_ini);
        $stmt->bindParam(':lng_ini', $lng_ini);
        $stmt->bindParam(':lat_fim', $lat_fim);
        $stmt->bindParam(':lng_fim', $lng_fim);
        $stmt->bindParam(':km', $km);
        $stmt->bindParam(':tempo', $tempo);
        $stmt->bindParam(':endereco_ini_txt', $endereco_ini_txt);
        $stmt->bindParam(':endereco_fim_txt', $endereco_fim_txt);
        $stmt->bindParam(':taxa', $taxa);
        $stmt->bindParam(':f_pagamento', $f_pagamento);
        $stmt->bindParam(':status_pagamento', $status_pagamento);
        $stmt->bindParam(':ref_pagamento', $ref_pagamento);
        $stmt->bindParam(':cupom', $cupom);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':nome_cliente', $nome_cliente);
        $stmt->execute();
        return $this->conexao->lastInsertId();
    }

    public function get_corrida_id($id)
    {
        $query = "SELECT * FROM corridas WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }

    public function get_all_corridas_cliente($cliente_id)
    {
        $query = "SELECT * FROM corridas WHERE cliente_id = :cliente_id ORDER BY id DESC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function getAllCorridasAbertasCliente($cliente_id)
    {
        $query = "SELECT * FROM corridas WHERE cliente_id = :cliente_id AND (status = 0 OR status = 1 OR status = 2 OR status = 3)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();

    }

    public function get_all_corridas_motorista($motorista_id)
    {
        $query = "SELECT * FROM corridas WHERE motorista_id = :motorista_id ORDER BY id DESC";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function get_all_corridas_cidade($cidade_id, $ativas = true)
    {
        if ($ativas) {
            $query = "SELECT * FROM corridas WHERE cidade_id = :cidade_id AND (status < 4 OR status = 9) ORDER BY id DESC";
        }
        else {
            $query = "SELECT * FROM corridas WHERE cidade_id = :cidade_id ORDER BY id DESC";
        }
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function set_status($id, $status)
    {
        $query = "UPDATE corridas SET status = :status WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function status_string($status_int)
    {
        $status_array = array(
            "0" => "Procurando Motorista",
            "1" => "Motorista a Caminho",
            "2" => "Motorista Chegou",
            "3" => "Em Viagem",
            "4" => "Finalizada",
            "5" => "Cancelada",
            "6" => "Aguardando Pagamento",
            "9" => "Taxímetro"
        );
        return $status_array[$status_int];
    }

    public function update_status_pagamento($id, $status_pagamento)
    {
        $query = "UPDATE corridas SET status_pagamento = :status_pagamento WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':status_pagamento', $status_pagamento);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function get_last_id_corrida($cidade_id)
    {
        $query = "SELECT id FROM corridas WHERE cidade_id = :cidade_id ORDER BY id DESC LIMIT 1";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        return $result['id'];
    }

    public function get_corridas_cidade_datas($cidade_id, $date_from, $date_to, $concluidos = false)
    {
        if ($concluidos) {
            $cmd = "SELECT * FROM corridas WHERE cidade_id = :cidade_id AND date BETWEEN :date_from AND :date_to AND status = '4' ORDER BY date ASC";
        }
        else {
            $cmd = "SELECT * FROM corridas WHERE cidade_id = :cidade_id AND date BETWEEN :date_from AND :date_to ORDER BY date ASC";
        }
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":date_from", $date_from);
        $sql->bindValue(":date_to", $date_to);
        $sql->execute();
        $corridas = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $corridas;
    }

    public function get_corridas_motorista_datas($motorista_id, $date_from, $date_to, $completas = false)
    {
        if ($completas) {
            $cmd = "SELECT * FROM corridas WHERE motorista_id = :motorista_id AND date BETWEEN :date_from AND :date_to AND status = '4' ORDER BY date ASC";
        }
        else {
            $cmd = "SELECT * FROM corridas WHERE motorista_id = :motorista_id AND date BETWEEN :date_from AND :date_to ORDER BY date ASC";
        }
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":motorista_id", $motorista_id);
        $sql->bindValue(":date_from", $date_from);
        $sql->bindValue(":date_to", $date_to);
        $sql->execute();
        $corridas = $sql->fetchAll(PDO::FETCH_ASSOC);
        if ($corridas) {
            return $corridas;
        }
        else {
            return false;
        }
    }


    public function altera_motorista($id, $motorista_id)
    {
        $query = "UPDATE corridas SET motorista_id = :motorista_id WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function aceitar($motorista_id, $id_corrida)
    {
        $query = "UPDATE corridas SET motorista_id = :motorista_id, status = '1' WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':motorista_id', $motorista_id);
        $stmt->bindParam(':id', $id_corrida);
        return $stmt->execute();
    }

    public function get_corridas_disponiveis($cidade_id)
    {
        $cmd = "SELECT * FROM corridas WHERE cidade_id = :cidade_id AND status = '0' ORDER BY date ASC";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->execute();
        $corridas = $sql->fetchAll(PDO::FETCH_ASSOC);
        if ($corridas) {
            return $corridas;
        }
        else {
            return false;
        }
    }

    public function get_corridas_abertas($motorista_id)
    {
        $cmd = "SELECT * FROM corridas WHERE motorista_id = :motorista_id AND (status = '1' OR status = '2' OR status = '3') ORDER BY date ASC";
        $sql = $this->conexao->prepare($cmd);
        $sql->bindValue(":motorista_id", $motorista_id);
        $sql->execute();
        $corridas = $sql->fetchAll(PDO::FETCH_ASSOC);
        if ($corridas) {
            return $corridas;
        }
        else {
            return false;
        }
    }

    public function update_taximetro($corrida_id, $taxa, $tempo, $km, $endereco_fim_txt)
    {
        $query = "UPDATE corridas SET taxa = :taxa, tempo = :tempo, km = :km, endereco_fim_txt = :endereco_fim_txt WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':taxa', $taxa);
        $stmt->bindParam(':tempo', $tempo);
        $stmt->bindParam(':km', $km);
        $stmt->bindParam(':endereco_fim_txt', $endereco_fim_txt);
        $stmt->bindParam(':id', $corrida_id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function cancelar_corrida($user_id)
    {
        $query = "UPDATE corridas SET status = '5' WHERE cliente_id = :id AND (status = '0' OR status = '1' OR status = '2' OR status = '3')";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }


    public function atualiza_taxa($id_corrida, $taxa_corrida)
    {
        $query = "UPDATE corridas SET taxa = :taxa_corrida WHERE id = :id_corrida";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':taxa_corrida', $taxa_corrida);
        $stmt->bindParam(':id_corrida', $id_corrida);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }

    }

    public function atualizaKm($km, $id_corrida)
    {
        $query = "UPDATE corridas SET km = :km WHERE id = :id_corrida";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':km', $km);
        $stmt->bindParam(':id_corrida', $id_corrida);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }

    }

    public function cancelarCorridaWA($user_whatsapp)
    {
        $query = "UPDATE corridas SET status = '5' WHERE user_whatsapp = :user_whatsapp AND (status = '0' OR status = '1' OR status = '2' OR status = '3')";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':user_whatsapp', $user_whatsapp);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }

    }

    public function getCorridaByWA($user_whatsapp)
    {
        $query = "SELECT * FROM corridas WHERE user_whatsapp = :user_whatsapp AND (status = '0' OR status = '1' OR status = '2' OR status = '3') LIMIT 1";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':user_whatsapp', $user_whatsapp);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetch();

    }

    public function setUserWhatsapp($corrida_id, $user_whatsapp)
    {
        $query = "UPDATE corridas SET user_whatsapp = :user_whatsapp WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':user_whatsapp', $user_whatsapp);
        $stmt->bindParam(':id', $corrida_id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }

    }

    public function setimg_user($id, $img)
    {
        $query = "UPDATE corridas SET img_user = :img WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':img', $img);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function setObs($id, $obs)
    {
        $query = "UPDATE corridas SET obs = :obs WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':obs', $obs);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        else {
            return false;
        }

    }


    public function getAllProcurandoMotorista()
    {
        $query = "SELECT * FROM corridas WHERE status = '0'";
        $stmt = $this->conexao->prepare($query);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    // --- NOVAS FUNÇÕES SPRINT 1.2 ---

    /**
     * Inicia a contagem de tempo de espera
     */
    public function setInicioEspera($id)
    {
        $query = "UPDATE corridas SET inicio_espera = NOW() WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Finaliza a espera, calcula o tempo e o valor baseado na taxa da categoria
     */
    public function finalizaEspera($id)
    {
        // Busca dados da corrida e da categoria para pegar a tx_espera
        $query = "SELECT c.inicio_espera, cat.tx_espera 
                  FROM corridas c 
                  JOIN categorias cat ON c.categoria_id = cat.id 
                  WHERE c.id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados && $dados['inicio_espera']) {
            $inicio = new DateTime($dados['inicio_espera']);
            $agora = new DateTime();
            $intervalo = $inicio->diff($agora);
            $minutos = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

            $valor_espera = $minutos * $dados['tx_espera'];

            $queryUpdate = "UPDATE corridas SET 
                            tempo_espera = :tempo, 
                            valor_espera = :valor, 
                            taxa = taxa + :valor,
                            inicio_espera = NULL 
                            WHERE id = :id";
            $stmtUpdate = $this->conexao->prepare($queryUpdate);
            $stmtUpdate->bindParam(':tempo', $minutos);
            $stmtUpdate->bindParam(':valor', $valor_espera);
            $stmtUpdate->bindParam(':id', $id);
            return $stmtUpdate->execute();
        }
        return false;
    }

    /**
     * Registra taxa de cancelamento
     */
    public function aplicaTaxaCancelamento($id, $valor)
    {
        $query = "UPDATE corridas SET taxa_cancelamento = :valor, taxa = :valor, status = '5' WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}