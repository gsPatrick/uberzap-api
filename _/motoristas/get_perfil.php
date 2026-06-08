<?php
include("../bd/config.php");
include("../classes/motoristas.php");
include("../classes/seguranca.php");

$secret_key = $_POST['secret'];
$s = new seguranca();

if ($s->compare_secret($secret_key)) {
    include("../bd/conexao.php");
    $id_motorista = $_POST['id_motorista'];
    if (!$id_motorista) {
        echo "no";
        exit;
    }
    

    $m = new motoristas();
    $dados = $m->get_motorista($id_motorista);
    
    if ($dados) {
        // Anexa as fotos de documentos/veículo (tabela motorista_docs), que NÃO
        // ficam na tabela motoristas. Casa por CPF ou telefone (só dígitos).
        try {
            global $pdo;
            $cpfDigits = preg_replace('/\D/', '', (string) ($dados['cpf'] ?? ''));
            $telDigits = preg_replace('/\D/', '', (string) ($dados['telefone'] ?? ''));
            $st = $pdo->prepare(
                "SELECT img_antecedente, img_cnh, img_documento, img_lateral, img_frente, img_selfie
                 FROM motorista_docs
                 WHERE REPLACE(REPLACE(REPLACE(cpf,'.',''),'-',''),' ','') = :cpf
                    OR REPLACE(REPLACE(REPLACE(REPLACE(telefone,'(',''),')',''),'-',''),' ','') = :tel
                 ORDER BY id DESC LIMIT 1"
            );
            $st->execute([':cpf' => $cpfDigits, ':tel' => $telDigits]);
            $docs = $st->fetch(PDO::FETCH_ASSOC);
            foreach (['img_antecedente', 'img_cnh', 'img_documento', 'img_lateral', 'img_frente', 'img_selfie'] as $k) {
                $dados[$k] = $docs[$k] ?? '';
            }
        } catch (Throwable $e) {
            error_log('[get_perfil.php] docs: ' . $e->getMessage());
        }

        // Garante que campos numéricos sejam strings para o JSON se necessário
        $dados['saldo'] = str_replace('.', ',', $dados['saldo']);
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    } else {
        echo "no";
    }
} else {
    echo "no_auth";
}
?>
