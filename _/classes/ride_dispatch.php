<?php
/**
 * Despacho de corrida com DEDUP à prova de duplicata.
 *
 * O banco é o gatilho: a tabela `corridas_dispatch` tem PK em corrida_id, então
 * só existe 1 linha por corrida. Quem conseguir INSERIR primeiro "ganha o claim"
 * e dispara o push; qualquer outro processo (cron, API, 2 ciclos cruzando) bate
 * na chave única e PULA — sem duplicar, sem erro.
 *
 * Usado por:
 *   - cron/dispatch_corridas.php  (cobre app/API ANTIGA — origem 'cron')
 *   - classes/corridas.php inline (cobre app/API NOVA   — origem 'api')
 */
require_once __DIR__ . '/expo_push.php';
require_once __DIR__ . '/uzlog.php';

class RideDispatch
{
    private static $tableReady = false;

    /** Cria a tabela de controle (idempotente). Na 1ª criação, marca todas as
     *  corridas EXISTENTES como já tratadas, pra não disparar corridas antigas. */
    public static function ensureTable()
    {
        if (self::$tableReady) {
            return;
        }
        global $pdo;
        $existed = $pdo->query("SHOW TABLES LIKE 'corridas_dispatch'")->fetch();
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS corridas_dispatch (
                corrida_id INT NOT NULL PRIMARY KEY,
                enviados INT NOT NULL DEFAULT 0,
                origem VARCHAR(20) NOT NULL DEFAULT 'cron',
                status VARCHAR(20) NOT NULL DEFAULT 'enviando',
                criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                atualizado_em DATETIME NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        if (!$existed) {
            // 1ª vez: tudo que já existe entra como 'pre-existente' (não dispara).
            $pdo->exec(
                "INSERT IGNORE INTO corridas_dispatch (corrida_id, origem, status)
                 SELECT id, 'init', 'pre-existente' FROM corridas"
            );
        }
        self::$tableReady = true;
    }

    /**
     * Reivindica a corrida (claim atômico) e, se ganhou, dispara o push.
     * @return int motoristas notificados; -1 se já estava despachada/inválida.
     */
    public static function dispatch(array $corrida, $origem = 'cron')
    {
        global $pdo;
        self::ensureTable();

        $id = (int) ($corrida['id'] ?? 0);
        if ($id <= 0) {
            return -1;
        }
        // Só corrida NOVA sem motorista (taxímetro/atribuída não dispara broadcast).
        if ((int) ($corrida['motorista_id'] ?? 0) !== 0) {
            return -1;
        }

        // CLAIM atômico: a PK corrida_id garante 1 dispatch por corrida.
        try {
            $ins = $pdo->prepare(
                "INSERT INTO corridas_dispatch (corrida_id, origem) VALUES (:id, :origem)"
            );
            $ins->execute([':id' => $id, ':origem' => $origem]);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return -1; // chave única => já despachada por outro processo
            }
            uzlog("[dispatch] #$id ERRO no claim: " . $e->getMessage());
            return -1;
        }

        // Ganhou o claim -> dispara.
        $cidade = $corrida['cidade_id'] ?? 0;
        $cat = $corrida['categoria_id'] ?? 0;
        uzlog("[dispatch] corrida #$id (cidade $cidade cat $cat origem $origem) -> push");
        $enviados = 0;
        try {
            $enviados = (int) ExpoPush::notifyOnlineDriversNewRide($cidade, $cat, $corrida);
        } catch (\Throwable $e) {
            uzlog("[dispatch] #$id ERRO no push: " . $e->getMessage());
        }
        try {
            $pdo->prepare(
                "UPDATE corridas_dispatch SET enviados = :e, status = 'enviado', atualizado_em = NOW() WHERE corrida_id = :id"
            )->execute([':e' => $enviados, ':id' => $id]);
        } catch (\Throwable $e) {
        }
        uzlog("[dispatch] corrida #$id push enviado para $enviados motorista(s)");
        return $enviados;
    }
}
