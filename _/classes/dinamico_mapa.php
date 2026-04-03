<?php
class dinamico_mapa
{
    private $pdo;
    private $conexao;
    public function __construct()
    {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function cadastra($cidade_id, $nome, $latitude, $longitude, $raio = 0, $adicional = "0,00")
    {
        $sql = "INSERT INTO dinamico_mapa (cidade_id, nome, latitude, longitude, raio, adicional) VALUES (:cidade_id, :nome, :latitude, :longitude, :raio, :adicional)";
        $sql = $this->conexao->prepare($sql);
        $sql->bindValue(":cidade_id", $cidade_id);
        $sql->bindValue(":nome", $nome);
        $sql->bindValue(":latitude", $latitude);
        $sql->bindValue(":longitude", $longitude); 
        $sql->bindValue(":raio", $raio);
        $sql->bindValue(":adicional", $adicional);
        $sql->execute();
        return true;
    }

    public function get_dinamico_mapa($cidade_id)
    {
        $query = "SELECT * FROM dinamico_mapa WHERE cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->execute();
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $dados;
    }

    public function delete_dinamico_mapa($id)
    {
        $query = "DELETE FROM dinamico_mapa WHERE id = :id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return true;
    }

    public function verifica_localizacao($cidade_id, $latitude, $longitude)
    {
        $query = "SELECT * FROM dinamico_mapa WHERE cidade_id = :cidade_id";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(":cidade_id", $cidade_id);
        $stmt->execute();
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($dados) {
            foreach ($dados as $dinamico) {
                $nome = $dinamico['nome'];
                $raio = $dinamico['raio'];
                $adicional = $dinamico['adicional'];
                $pertence = $this->estaDentroDoRaio($latitude, $longitude, $raio, $dinamico['latitude'], $dinamico['longitude']);
                if ($pertence) {
                    return array("nome" => $nome, "adicional" => $adicional);
                    break;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    private function calcularDistanciaHaversine($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Raio médio da Terra em metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    private function estaDentroDoRaio($lat, $lng, $raio, $centroLat, $centroLng)
    {
        $distancia = $this->calcularDistanciaHaversine($lat, $lng, $centroLat, $centroLng);
        return $distancia <= $raio;
    }
}
