<?php
Class categorias {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastrar($cidade_id, $nome, $descricao, $img, $tx_km, $tx_minuto, $tx_minima, $tx_base, $raio,  $dinamico_horarios = "", $dinamico_local = "", $ativa = 1, $ordem = 0){
        if($dinamico_horarios != ""){
            $dinamico_horarios = serialize($dinamico_horarios);
        }
        if($dinamico_local != ""){
            $dinamico_local = serialize($dinamico_local);
        }
        $cmd = $this->conexao->prepare("INSERT INTO categorias (cidade_id, nome, descricao, dinamico_horarios, dinamico_local, img, tx_km, tx_minuto, tx_minima, tx_base, raio, ativa, ordem) VALUES (:cidade_id, :nome, :descricao, :dinamico_horarios, :dinamico_local, :img, :tx_km, :tx_minuto, :tx_minima, :tx_base, :raio, :ativa, :ordem)");
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->bindValue(":nome", $nome);
        $cmd->bindValue(":descricao", $descricao);
        $cmd->bindValue(":dinamico_horarios", $dinamico_horarios);
        $cmd->bindValue(":dinamico_local", $dinamico_local);
        $cmd->bindValue(":img", $img);
        $cmd->bindValue(":tx_km", $tx_km);
        $cmd->bindValue(":tx_minuto", $tx_minuto);
        $cmd->bindValue(":tx_minima", $tx_minima);
        $cmd->bindValue(":tx_base", $tx_base);
        $cmd->bindValue(":raio", $raio);
        $cmd->bindValue(":ativa", $ativa);
        $cmd->bindValue(":ordem", $ordem);
        $cmd->execute();
        return true;
    }

    public function edit_categoria($id, $cidade_id, $nome, $descricao, $img, $tx_km, $tx_minuto, $tx_minima, $tx_base, $raio, $dinamico_horarios = "", $dinamico_local = "", $ativa = 1, $ordem = 0){
        if($dinamico_horarios != ""){
            $dinamico_horarios = serialize($dinamico_horarios);
        }
        if($dinamico_local != ""){
            $dinamico_local = serialize($dinamico_local);
        }
        $cmd = $this->conexao->prepare("UPDATE categorias SET cidade_id = :cidade_id, nome = :nome, descricao = :descricao, dinamico_horarios = :dinamico_horarios, dinamico_local = :dinamico_local, img = :img, tx_km = :tx_km, tx_minuto = :tx_minuto, tx_minima = :tx_minima, tx_base = :tx_base, raio = :raio, ativa = :ativa, ordem = :ordem WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->bindValue(":nome", $nome);
        $cmd->bindValue(":descricao", $descricao);
        $cmd->bindValue(":dinamico_horarios", $dinamico_horarios);
        $cmd->bindValue(":dinamico_local", $dinamico_local);
        $cmd->bindValue(":img", $img);
        $cmd->bindValue(":tx_km", $tx_km);
        $cmd->bindValue(":tx_minuto", $tx_minuto);
        $cmd->bindValue(":tx_minima", $tx_minima);
        $cmd->bindValue(":tx_base", $tx_base); 
        $cmd->bindValue(":raio", $raio);
        $cmd->bindValue(":ativa", $ativa);
        $cmd->bindValue(":ordem", $ordem);
        $cmd->execute();
        return true;
    }

    public function get_categoria($id){
        $cmd = $this->conexao->prepare("SELECT * FROM categorias WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        //unserialize dinamico_horarios and dinamico_local for all data
        foreach ($res as $key => $value) {
            $res[$key]['dinamico_horarios'] = unserialize($value['dinamico_horarios']);
            $res[$key]['dinamico_local'] = unserialize($value['dinamico_local']);
        }
        return $res;
    }

    public function get_categorias($cidade_id, $ativa = true){
        if($ativa){
            $cmd = $this->conexao->prepare("SELECT * FROM categorias WHERE cidade_id = :cidade_id AND ativa = 1 ORDER BY ordem ASC");
        }else{
            $cmd = $this->conexao->prepare("SELECT * FROM categorias WHERE cidade_id = :cidade_id ORDER BY ordem ASC");
        }
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        //unserialize dinamico_horarios and dinamico_local for all data
        foreach ($res as $key => $value) {
            $res[$key]['dinamico_horarios'] = unserialize($value['dinamico_horarios']);
            $res[$key]['dinamico_local'] = unserialize($value['dinamico_local']);
        }
        return $res;
    }

    public function del_categoria($id){
        $cmd = $this->conexao->prepare("DELETE FROM categorias WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
    }

}
