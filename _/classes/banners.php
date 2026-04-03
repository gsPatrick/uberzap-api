<?php
class banners
{
    private $pdo;
    private $conexao;
    public function __construct()
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastra($cidade_id, $img, $link, $ativo)
    {
        $query = "INSERT INTO banners (cidade_id, img, link, ativo) VALUES (:cidade_id, :img, :link, :ativo)";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->bindParam(':img', $img);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':ativo', $ativo);
        $stmt->execute();
        return $this->conexao->lastInsertId();
    }

    public function get_banners($cidade_id)
    {
        $query = "SELECT * FROM banners WHERE cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':cidade_id', $cidade_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $stmt->fetchAll();
    }

    public function get_banner($banner_id)
    {
        $query = "SELECT * FROM banners WHERE id = :banner_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':banner_id', $banner_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        if ($banner = $stmt->fetch()) {
            return $banner;
        } else {
            return false;
        }
    }

    public function alterarStatus($id, $ativo)
    {
        $query = "UPDATE banners SET ativo = :ativo WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ativo', $ativo);
        return $stmt->execute();
    
    }

    public function deletar($id)
    {
        $query = "DELETE FROM banners WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function editar($id, $img, $link, $ativo)
    {
        $query = "UPDATE banners SET img = :img, link = :link, ativo = :ativo WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':img', $img);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':ativo', $ativo);
        return $stmt->execute();
    }
}
