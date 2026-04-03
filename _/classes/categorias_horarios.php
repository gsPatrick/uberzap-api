<?php
class dinamico_horarios
{
    private $pdo;
    private $conexao;
    public function __construct()
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cad_dinamico_horario($cidade_id, $nome, $segunda, $terca, $quarta, $quinta, $sexta, $sabado, $domingo, $adicional = "0,00")
    {
        $cmd = $this->conexao->prepare("INSERT INTO dinamico_horarios (cidade_id, nome, segunda, terca, quarta, quinta, sexta, sabado, domingo, adicional) VALUES (:cidade_id, :nome, :segunda, :terca, :quarta, :quinta, :sexta, :sabado, :domingo, :adicional)");
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->bindValue(":nome", $nome);
        $cmd->bindValue(":segunda", $segunda);
        $cmd->bindValue(":terca", $terca);
        $cmd->bindValue(":quarta", $quarta);
        $cmd->bindValue(":quinta", $quinta);
        $cmd->bindValue(":sexta", $sexta);
        $cmd->bindValue(":sabado", $sabado);
        $cmd->bindValue(":domingo", $domingo);
        $cmd->bindValue(":adicional", $adicional);
        $cmd->execute();
    }

    public  function get_dinamico_horarios($cidade_id, $nome)
    {
        $cmd = $this->conexao->prepare("SELECT * FROM dinamico_horarios WHERE cidade_id = :cidade_id AND nome = :nome");
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->bindValue(":nome", $nome);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function get_dinamico_horarios_loja($cidade_id)
    {
        $cmd = $this->conexao->prepare("SELECT * FROM dinamico_horarios WHERE cidade_id = :cidade_id");
        $cmd->bindValue(":cidade_id", $cidade_id);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function get_dinamico_horarios_id($id)
    {
        $cmd = $this->conexao->prepare("SELECT * FROM dinamico_horarios WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function edit_dinamico_horarios($id, $nome, $segunda, $terca, $quarta, $quinta, $sexta, $sabado, $domingo, $adicional = "0,00")
    {
        $cmd = $this->conexao->prepare("UPDATE dinamico_horarios SET nome = :nome, segunda = :segunda, terca = :terca, quarta = :quarta, quinta = :quinta, sexta = :sexta, sabado = :sabado, domingo = :domingo, adicional = :adicional WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->bindValue(":nome", $nome);
        $cmd->bindValue(":segunda", $segunda);
        $cmd->bindValue(":terca", $terca);
        $cmd->bindValue(":quarta", $quarta);
        $cmd->bindValue(":quinta", $quinta);
        $cmd->bindValue(":sexta", $sexta);
        $cmd->bindValue(":sabado", $sabado);
        $cmd->bindValue(":domingo", $domingo);
        $cmd->bindValue(":adicional", $adicional);
        $cmd->execute();
    }

    public function del_dinamico_horarios($id)
    {
        $cmd = $this->conexao->prepare("DELETE FROM dinamico_horarios WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
    }

    public function verifica_horario($id)
    {
        $dinamico = $this->get_horarios($id);
        $horarios = $this->horarios_do_dia($dinamico['id'], $this->dia_da_semana());
        $nome = $dinamico['nome'];
        $adicional = $dinamico['adicional'];
        if ($this->hora($horarios['manha_ini'], $horarios['manha_fim'])) {
            return array("nome" => $nome, "adicional" => $adicional);

        } else if ($this->hora($horarios['noite_ini'], $horarios['noite_fim'])) {
            return array("nome" => $nome, "adicional" => $adicional);

        }
        return false;
    }

    private function get_horarios($id)
    {
        $sql = "SELECT * FROM dinamico_horarios WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
    }

    private function horarios_do_dia($id, $dia)
    {
        $horarios = $this->get_horarios($id);
        if ($dia == "segunda") {
            return unserialize($horarios['segunda']);
        } else if ($dia == "terca") {
            return unserialize($horarios['terca']);
        } else if ($dia == "quarta") {
            return unserialize($horarios['quarta']);
        } else if ($dia == "quinta") {
            return unserialize($horarios['quinta']);
        } else if ($dia == "sexta") {
            return unserialize($horarios['sexta']);
        } else if ($dia == "sabado") {
            return unserialize($horarios['sabado']);
        } else if ($dia == "domingo") {
            return unserialize($horarios['domingo']);
        }
    }

    private function dia_da_semana()
    {
        $dia = date('w');
        if ($dia == 0) {
            return "domingo";
        } else if ($dia == 1) {
            return "segunda";
        } else if ($dia == 2) {
            return "terca";
        } else if ($dia == 3) {
            return "quarta";
        } else if ($dia == 4) {
            return "quinta";
        } else if ($dia == 5) {
            return "sexta";
        } else if ($dia == 6) {
            return "sabado";
        }
    }

    private function hora($ini, $fim)
    {
        $start = new DateTime($ini);
        $end = new DateTime($fim);
        $now = new DateTime('now');
        if ($start <= $now && $now <= $end) {
            return true;
        } else {
            return false;
        }
    }
}
