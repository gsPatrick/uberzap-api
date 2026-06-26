<?php

require_once __DIR__ . '/motoristas.php';

/**
 * Push notifications via Expo Push API (app React Native / EAS).
 * Token: ExponentPushToken[xxxx] — salvo em id_signal (clientes/motoristas).
 */
class ExpoPush
{
    const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    public static function isExpoToken($token)
    {
        $t = trim((string) $token);
        return $t !== '' && (
            stripos($t, 'ExponentPushToken[') === 0 ||
            stripos($t, 'ExpoPushToken[') === 0
        );
    }

    /**
     * @return array{ok:bool,response?:string}
     */
    public static function send($token, $title, $body, $data = [])
    {
        if (!self::isExpoToken($token)) {
            return ['ok' => false];
        }

        $type = $data['type'] ?? 'general';
        $channelId = $data['channelId'] ?? ($type === 'ride_alert' ? 'ride_alert' : 'trip_status');

        $message = [
            'to' => trim($token),
            'title' => (string) $title,
            'body' => (string) $body,
            'sound' => 'default',
            'priority' => 'high',
            'channelId' => $channelId,
            'data' => array_merge(
                [
                    'type' => $type,
                    'channelId' => $channelId,
                ],
                $data
            ),
        ];

        return self::postMessages([$message]);
    }

    /**
     * Envia várias mensagens (até 100 por request Expo).
     * @param array<int,array> $messages
     */
    public static function sendBatch(array $messages)
    {
        if (empty($messages)) {
            return ['ok' => true, 'sent' => 0];
        }

        $chunks = array_chunk($messages, 100);
        $sent = 0;
        foreach ($chunks as $chunk) {
            $result = self::postMessages($chunk);
            if ($result['ok']) {
                $sent += count($chunk);
            }
        }
        return ['ok' => $sent > 0, 'sent' => $sent];
    }

    private static function postMessages(array $messages)
    {
        $ch = curl_init(self::EXPO_PUSH_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($messages),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        require_once __DIR__ . '/uzlog.php';
        uzlog("[push] Expo respondeu HTTP $httpCode", substr((string) $response, 0, 600));

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('[ExpoPush] HTTP ' . $httpCode . ' — ' . $response);
            return ['ok' => false, 'response' => $response];
        }

        return ['ok' => true, 'response' => $response];
    }

    // ─── Passageiro ───────────────────────────────────────────────

    public static function notifyPassengerTripStatus($cliente, $status, $motoristaNome = 'Motorista', $rideId = null)
    {
        if (!$cliente || empty($cliente['id_signal'])) {
            return false;
        }

        $messages = [
            0 => ['Buscando motorista...', 'Sua corrida foi solicitada. Estamos procurando um motorista para você.'],
            1 => ['Motorista a caminho!', $motoristaNome . ' aceitou sua corrida e está indo até você.'],
            2 => ['Motorista no local!', 'Seu motorista chegou ao ponto de embarque.'],
            3 => ['Corrida iniciada!', 'Boa viagem! Você está a caminho do destino.'],
            4 => ['Corrida finalizada!', 'Sua viagem foi encerrada. Obrigado por usar o UbeZap!'],
            5 => ['Corrida cancelada', 'A corrida foi cancelada.'],
        ];

        $st = (int) $status;
        if (!isset($messages[$st])) {
            return false;
        }

        list($title, $body) = $messages[$st];
        $data = [
            'type' => 'trip_status',
            'status' => $st,
            'channelId' => 'trip_status',
        ];
        if ($rideId) {
            $data['rideId'] = (string) $rideId;
        }

        $result = self::send($cliente['id_signal'], $title, $body, $data);
        return $result['ok'];
    }

    public static function notifyPassengerByClienteId($clienteId, $status, $motoristaNome = 'Motorista', $rideId = null)
    {
        require_once __DIR__ . '/clientes.php';
        $cl = new Clientes();
        $cliente = $cl->get_cliente_id($clienteId);
        if (!$cliente) {
            return false;
        }
        return self::notifyPassengerTripStatus($cliente, $status, $motoristaNome, $rideId);
    }

    // ─── Motorista ────────────────────────────────────────────────

    public static function notifyDriverNewRide($motorista, $ride)
    {
        if (!$motorista || empty($motorista['id_signal'])) {
            return false;
        }

        $pickup = $ride['endereco_ini_txt'] ?? $ride['endereco_ini'] ?? 'Embarque';
        $dest = $ride['endereco_fim_txt'] ?? $ride['endereco_fim'] ?? 'Destino';
        $taxa = $ride['taxa'] ?? '';
        $price = $taxa ? 'R$ ' . str_replace('.', ',', (string) $taxa) : '';

        $result = self::send(
            $motorista['id_signal'],
            'Nova corrida disponível!',
            trim("$price — $pickup → $dest"),
            [
                'type' => 'ride_alert',
                'rideId' => (string) ($ride['id'] ?? ''),
                'channelId' => 'ride_alert',
            ]
        );
        return $result['ok'];
    }

    public static function notifyDriver($motorista, $title, $body, $data = [])
    {
        if (!$motorista || empty($motorista['id_signal'])) {
            return false;
        }
        $payload = array_merge([
            'type' => 'ride_alert',
            'channelId' => 'ride_alert',
        ], $data);
        $result = self::send($motorista['id_signal'], $title, $body, $payload);
        return $result['ok'];
    }

    public static function notifyDriverById($motoristaId, $title, $body, $data = [])
    {
        $m = new Motoristas();
        $motorista = $m->get_motorista($motoristaId);
        if (!$motorista || empty($motorista['id'])) {
            return false;
        }
        return self::notifyDriver($motorista, $title, $body, $data);
    }

    public static function notifyDriverPassengerCancelled($motorista, $rideId = null)
    {
        return self::notifyDriver(
            $motorista,
            'Corrida cancelada',
            'O passageiro cancelou a corrida.',
            [
                'type' => 'ride_alert',
                'event' => 'passenger_cancelled',
                'rideId' => $rideId ? (string) $rideId : '',
            ]
        );
    }

    public static function notifyDriverPassengerCancelledById($motoristaId, $rideId = null)
    {
        $m = new Motoristas();
        $motorista = $m->get_motorista($motoristaId);
        return self::notifyDriverPassengerCancelled($motorista, $rideId);
    }

    /**
     * Nova corrida: notifica todos os motoristas online/disponíveis da cidade (categoria compatível).
     */
    public static function notifyOnlineDriversNewRide($cidadeId, $categoriaId, $corrida)
    {
        require_once __DIR__ . '/uzlog.php';
        require_once __DIR__ . '/fcm_v1.php';
        $fcm_sent = 0;
        $m = new Motoristas();
        $motoristas = $m->get_motoristas_online_disponiveis($cidadeId);
        if (!$motoristas || !is_array($motoristas)) {
            uzlog("[push] cidade=$cidadeId: NENHUM motorista online/disponivel");
            return 0;
        }
        uzlog("[push] cidade=$cidadeId cat=$categoriaId: " . count($motoristas) . " motorista(s) online/disponivel");

        $pickup = $corrida['endereco_ini_txt'] ?? $corrida['endereco_ini'] ?? 'Embarque';
        $dest = $corrida['endereco_fim_txt'] ?? $corrida['endereco_fim'] ?? 'Destino';
        $taxa = $corrida['taxa'] ?? '';
        $price = $taxa ? 'R$ ' . str_replace('.', ',', (string) $taxa) : '';
        $rideId = (string) ($corrida['id'] ?? '');

        $skip_cat = 0;
        $skip_token = 0;
        $batch = [];
        foreach ($motoristas as $motorista) {
            if (!self::motoristaAceitaCategoria($motorista, $categoriaId)) {
                $skip_cat++;
                uzlog("[push]   motorista #" . ($motorista['id'] ?? '?') . " IGNORADO: categoria $categoriaId incompativel (ids=" . ($motorista['ids_categorias'] ?? '[]') . ")");
                continue;
            }
            // Dados da corrida (usados tanto pelo FCM-direto quanto pelo Expo).
            $rideData = [
                'type' => 'ride_alert',
                'rideId' => $rideId,
                'channelId' => 'ride_alert',
                'taxa' => (string) ($corrida['taxa'] ?? ''),
                'endereco_ini_txt' => $corrida['endereco_ini_txt'] ?? ($corrida['endereco_ini'] ?? ''),
                'endereco_fim_txt' => $corrida['endereco_fim_txt'] ?? ($corrida['endereco_fim'] ?? ''),
                'nome_cliente' => $corrida['nome_cliente'] ?? ($corrida['cliente'] ?? ''),
                'nota_cliente' => (string) ($corrida['nota_cliente'] ?? ''),
                'km' => (string) ($corrida['km'] ?? ''),
                'tempo' => (string) ($corrida['tempo'] ?? ''),
                'f_pagamento' => $corrida['f_pagamento'] ?? '',
                'cidade_id' => (string) ($corrida['cidade_id'] ?? $cidadeId),
                'categoria_id' => (string) ($corrida['categoria_id'] ?? $categoriaId),
            ];

            // BUILD NOVO: tem fcm_token -> FCM DIRETO (data-only, high priority).
            // O app recebe via setBackgroundMessageHandler MESMO MORTO e desenha o
            // card full-screen (Notifee) + som. (É o único jeito de ter overlay com
            // o app fechado no Android.)
            $fcmToken = trim((string) ($motorista['fcm_token'] ?? ''));
            if ($fcmToken !== '' && FcmV1::isConfigured()) {
                $r = FcmV1::sendData($fcmToken, $rideData);
                uzlog("[push]   motorista #" . ($motorista['id'] ?? '?') . " FCM-direto HTTP " . ($r['code'] ?? '-') . ($r['ok'] ? ' OK' : (' FALHOU ' . ($r['error'] ?? '') . ' ' . substr((string) ($r['response'] ?? ''), 0, 180))));
                if (!empty($r['ok'])) { $fcm_sent++; continue; }
                // FCM falhou: cai pro Expo abaixo como fallback.
            }

            // BUILD ATUAL (sem fcm_token): Expo NOTIFICATION message — notificação +
            // som com o app fechado (sem overlay).
            if (!self::isExpoToken($motorista['id_signal'] ?? '')) {
                $skip_token++;
                uzlog("[push]   motorista #" . ($motorista['id'] ?? '?') . " IGNORADO: sem fcm_token e sem token Expo valido");
                continue;
            }
            uzlog("[push]   motorista #" . ($motorista['id'] ?? '?') . " OK -> Expo (token " . substr((string) $motorista['id_signal'], 0, 18) . "...)");
            $batch[] = [
                'to' => trim($motorista['id_signal']),
                'title' => 'Nova corrida disponível!',
                'body' => trim("$price — $pickup → $dest"),
                'sound' => 'default',
                'priority' => 'high',
                'channelId' => 'ride_alert',
                'data' => $rideData,
            ];
        }

        $expo_sent = 0;
        if (!empty($batch)) {
            $result = self::sendBatch($batch);
            $expo_sent = (int) ($result['sent'] ?? 0);
        }
        uzlog("[push] resumo: FCM-direto=$fcm_sent Expo=$expo_sent (cat_incompat=$skip_cat sem_token=$skip_token)");
        return $fcm_sent + $expo_sent;
    }

    /**
     * Avisa motoristas online que a corrida não está mais disponível (aceita por outro, cancelada, expirada).
     */
    public static function notifyOnlineDriversRideUnavailable($cidadeId, $categoriaId, $rideId, $exceptMotoristaId = null, $body = 'A corrida não está mais disponível.')
    {
        $m = new Motoristas();
        $motoristas = $m->get_motoristas_online_disponiveis($cidadeId);
        if (!$motoristas || !is_array($motoristas)) {
            return 0;
        }

        $rideIdStr = (string) $rideId;
        $exceptId = $exceptMotoristaId !== null ? (int) $exceptMotoristaId : null;
        $batch = [];

        foreach ($motoristas as $motorista) {
            if ($exceptId !== null && (int) ($motorista['id'] ?? 0) === $exceptId) {
                continue;
            }
            if (!self::motoristaAceitaCategoria($motorista, $categoriaId)) {
                continue;
            }
            if (!self::isExpoToken($motorista['id_signal'] ?? '')) {
                continue;
            }

            // DATA-ONLY: a task em background remove o card e para o som sem
            // mostrar nenhuma notificação visível pro motorista.
            $batch[] = [
                'to' => trim($motorista['id_signal']),
                'priority' => 'high',
                '_contentAvailable' => true,
                'data' => [
                    'type' => 'ride_alert',
                    'event' => 'ride_unavailable',
                    'rideId' => $rideIdStr,
                    'channelId' => 'ride_alert',
                ],
            ];
        }

        if (empty($batch)) {
            return 0;
        }

        $result = self::sendBatch($batch);
        return (int) ($result['sent'] ?? 0);
    }

    private static function motoristaAceitaCategoria($motorista, $categoriaId)
    {
        $ids = json_decode($motorista['ids_categorias'] ?? '[]', true);
        if (!is_array($ids)) {
            return false;
        }
        $cat = (string) $categoriaId;
        foreach ($ids as $id) {
            if ((string) $id === $cat) {
                return true;
            }
        }
        return false;
    }
}
