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
        if (!$motorista || empty($motorista['id_signal']) || !self::isExpoToken($motorista['id_signal'])) {
            return false;
        }

        // Data-only: sem título/texto. O app do motorista desenha o card full-screen
        // (com infos da corrida + Aceitar/Recusar) a partir destes dados.
        $message = self::buildDriverRideMessage($motorista['id_signal'], $ride);
        $result = self::postMessages([$message]);
        return $result['ok'];
    }

    /** Dados da corrida enviados no push data-only para o card rico do motorista. */
    private static function driverRideData($corrida, $extra = [])
    {
        return array_merge([
            'type' => 'ride_alert',
            'channelId' => 'ride_alert',
            'rideId' => (string) ($corrida['id'] ?? ''),
            'taxa' => (string) ($corrida['taxa'] ?? ''),
            'endereco_ini_txt' => (string) ($corrida['endereco_ini_txt'] ?? $corrida['endereco_ini'] ?? ''),
            'endereco_fim_txt' => (string) ($corrida['endereco_fim_txt'] ?? $corrida['endereco_fim'] ?? ''),
            'km' => (string) ($corrida['km'] ?? ''),
            'tempo' => (string) ($corrida['tempo'] ?? ''),
            'f_pagamento' => (string) ($corrida['forma_pagamento'] ?? $corrida['f_pagamento'] ?? ''),
            'nome_cliente' => (string) ($corrida['nome_cliente'] ?? $corrida['nome'] ?? $corrida['cliente'] ?? ''),
            'nota_cliente' => (string) ($corrida['nota_cliente'] ?? ''),
            'cidade_id' => (string) ($corrida['cidade_id'] ?? ''),
            'categoria_id' => (string) ($corrida['categoria_id'] ?? ''),
        ], $extra);
    }

    /** Mensagem Expo "data-only" (sem title/body) de alerta de corrida. */
    private static function buildDriverRideMessage($token, $corrida, $extra = [])
    {
        return [
            'to' => trim($token),
            'data' => self::driverRideData($corrida, $extra),
            'priority' => 'high',
            'channelId' => 'ride_alert',
            '_contentAvailable' => true,
        ];
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
        if (!$motorista || empty($motorista['id_signal']) || !self::isExpoToken($motorista['id_signal'])) {
            return false;
        }
        // Data-only: fecha o card do motorista e para o som.
        $message = [
            'to' => trim($motorista['id_signal']),
            'priority' => 'high',
            'channelId' => 'ride_alert',
            '_contentAvailable' => true,
            'data' => [
                'type' => 'ride_alert',
                'event' => 'passenger_cancelled',
                'rideId' => $rideId ? (string) $rideId : '',
                'channelId' => 'ride_alert',
            ],
        ];
        $result = self::postMessages([$message]);
        return $result['ok'];
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
        $m = new Motoristas();
        $motoristas = $m->get_motoristas_online_disponiveis($cidadeId);
        if (!$motoristas || !is_array($motoristas)) {
            return 0;
        }

        $batch = [];
        foreach ($motoristas as $motorista) {
            if (!self::motoristaAceitaCategoria($motorista, $categoriaId)) {
                continue;
            }
            if (!self::isExpoToken($motorista['id_signal'] ?? '')) {
                continue;
            }

            // Data-only: o app desenha o card full-screen (estilo Uber).
            $batch[] = self::buildDriverRideMessage($motorista['id_signal'], $corrida);
        }

        if (empty($batch)) {
            return 0;
        }

        $result = self::sendBatch($batch);
        return (int) ($result['sent'] ?? 0);
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

            // Data-only: o app fecha o card e para o som, sem mostrar texto cru.
            $batch[] = [
                'to' => trim($motorista['id_signal']),
                'priority' => 'high',
                'channelId' => 'ride_alert',
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
