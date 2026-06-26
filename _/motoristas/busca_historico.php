<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bd/conexao.php';
require_once __DIR__ . '/../classes/corridas.php';
require_once __DIR__ . '/../classes/seguranca.php';

function ubezap_format_hora_corrida($dateRaw)
{
    if (!$dateRaw) {
        return '--:--';
    }
    $parts = explode(' ', trim((string) $dateRaw));
    if (isset($parts[1]) && $parts[1] !== '') {
        return substr($parts[1], 0, 5);
    }
    return '--:--';
}

function ubezap_format_ride_row($row, corridas $c)
{
    $status = (string) ($row['status'] ?? '');
    return [
        'id' => $row['id'] ?? null,
        'status' => $status,
        'status_label' => $c->status_string($status) ?? 'Desconhecido',
        'hora' => ubezap_format_hora_corrida($row['date'] ?? ''),
        'valor' => $row['taxa'] ?? '0.00',
        'taxa' => $row['taxa'] ?? '0.00',
        'endereco_ini' => $row['endereco_ini_txt'] ?? '',
        'endereco_fim' => $row['endereco_fim_txt'] ?? '',
        'endereco_ini_txt' => $row['endereco_ini_txt'] ?? '',
        'endereco_fim_txt' => $row['endereco_fim_txt'] ?? '',
        'lat_ini' => $row['lat_ini'] ?? null,
        'lng_ini' => $row['lng_ini'] ?? null,
        'lat_fim' => $row['lat_fim'] ?? null,
        'lng_fim' => $row['lng_fim'] ?? null,
        'km' => $row['km'] ?? null,
        'tempo' => $row['tempo'] ?? null,
        'f_pagamento' => $row['f_pagamento'] ?? '',
        'metodo_pagamento' => $row['f_pagamento'] ?? '',
        'nome_cliente' => $row['nome_cliente'] ?? 'Passageiro',
        'cliente_id' => $row['cliente_id'] ?? null,
        'img_user' => $row['img_user'] ?? '',
        'foto_cliente' => $row['img_user'] ?? '',
        'user_whatsapp' => $row['user_whatsapp'] ?? '',
        'obs' => $row['obs'] ?? '',
        'categoria_id' => $row['categoria_id'] ?? null,
        'avaliacao' => null,
        'date' => $row['date'] ?? '',
    ];
}

try {
    $secret_key = $_POST['secret'] ?? '';
    $s = new seguranca();
    if (!$s->compare_secret($secret_key)) {
        http_response_code(401);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id_motorista = (int) ($_POST['id_motorista'] ?? 0);
    if ($id_motorista < 1) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $c = new corridas();
    $modo = strtolower(trim((string) ($_POST['modo'] ?? '')));
    $data = trim((string) ($_POST['data'] ?? ''));

    $corridas = [];

    if ($modo === 'recent' || $modo === 'todas' || $data === '') {
        // Filtro de status + LIMIT no SQL (com índice) — não varre a tabela toda.
        $raw = $c->get_historico_motorista($id_motorista, 100);
        if (is_array($raw)) {
            $corridas = $raw;
        }
    } else {
        $dataNorm = str_replace('/', '-', $data);
        $ts = strtotime($dataNorm);
        if ($ts) {
            $date_from = date('Y-m-d 00:00:00', $ts);
            $date_to = date('Y-m-d 23:59:59', $ts);
            $found = $c->get_corridas_motorista_datas($id_motorista, $date_from, $date_to);
            if (is_array($found)) {
                $corridas = $found;
            }
        }
    }

    // Avaliação por corrida (JOIN manual com a tabela avaliacoes)
    $avStmt = $pdo->prepare(
        "SELECT nota, comentario, date FROM avaliacoes WHERE corrida_id = :cid ORDER BY id DESC LIMIT 1"
    );

    $out = [];
    foreach ($corridas as $row) {
        $item = ubezap_format_ride_row($row, $c);
        if (!empty($row['id'])) {
            $avStmt->execute([':cid' => $row['id']]);
            $av = $avStmt->fetch(PDO::FETCH_ASSOC);
            if ($av) {
                $item['avaliacao'] = [
                    'nota' => (int) ($av['nota'] ?? 0),
                    'comentario' => $av['comentario'] ?? '',
                    'date' => $av['date'] ?? '',
                ];
            }
        }
        $out[] = $item;
    }

    echo json_encode($out, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('[busca_historico.php] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao buscar histórico'], JSON_UNESCAPED_UNICODE);
}
