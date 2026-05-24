<?php

/**
 * Envio de push via Expo Push API (app React Native / EAS).
 * Token formato: ExponentPushToken[xxxx]
 */
class ExpoPush
{
    public static function isExpoToken($token)
    {
        $t = trim((string) $token);
        return $t !== '' && (
            stripos($t, 'ExponentPushToken[') === 0 ||
            stripos($t, 'ExpoPushToken[') === 0
        );
    }

    public static function send($token, $title, $body, $data = [])
    {
        if (!self::isExpoToken($token)) {
            return false;
        }

        $payload = [
            'to' => trim($token),
            'title' => (string) $title,
            'body' => (string) $body,
            'sound' => 'default',
            'priority' => 'high',
            'data' => array_merge(['type' => 'trip_status'], $data),
        ];

        if (isset($data['channelId'])) {
            $payload['channelId'] = $data['channelId'];
        } elseif (isset($data['type']) && $data['type'] === 'ride_alert') {
            $payload['channelId'] = 'ride_alert';
        } else {
            $payload['channelId'] = 'trip_status';
        }

        $ch = curl_init('https://exp.host/--/api/v2/push/send');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log('[ExpoPush] HTTP ' . $httpCode . ' — ' . $response);
            return false;
        }

        return true;
    }

    public static function notifyPassengerTripStatus($cliente, $status, $motoristaNome = 'Motorista')
    {
        if (!$cliente || empty($cliente['id_signal'])) {
            return false;
        }

        $messages = [
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
        return self::send($cliente['id_signal'], $title, $body, [
            'type' => 'trip_status',
            'status' => $st,
            'channelId' => 'trip_status',
        ]);
    }

    public static function notifyDriverNewRide($motorista, $ride)
    {
        if (!$motorista || empty($motorista['id_signal'])) {
            return false;
        }

        $pickup = $ride['endereco_ini_txt'] ?? $ride['endereco_ini'] ?? 'Embarque';
        $dest = $ride['endereco_fim_txt'] ?? $ride['endereco_fim'] ?? 'Destino';
        $taxa = $ride['taxa'] ?? '';
        $price = $taxa ? 'R$ ' . str_replace('.', ',', $taxa) : '';

        return self::send(
            $motorista['id_signal'],
            'Nova corrida disponível!',
            trim("$price — $pickup → $dest"),
            [
                'type' => 'ride_alert',
                'rideId' => (string) ($ride['id'] ?? ''),
                'channelId' => 'ride_alert',
            ]
        );
    }
}
