<?php
/**
 * Envio FCM v1 DIRETO (sem Expo) usando a service account do Firebase.
 *
 * É o canal usado para o alerta de corrida do motorista: manda DATA-ONLY com
 * prioridade alta -> o app (com @react-native-firebase/messaging) recebe via
 * setBackgroundMessageHandler MESMO COM O APP MORTO e desenha o card full-screen
 * (Notifee) + som. (O push data-only via Expo NÃO chega com app morto no Android.)
 *
 * A service account vem de:
 *   1) env FIREBASE_SERVICE_ACCOUNT (o JSON inteiro), ou
 *   2) arquivo _/credentials/firebase-service-account.json
 */
class FcmV1
{
    private static $accessToken = null;
    private static $accessTokenExp = 0;
    private static $sa = null;

    private static function serviceAccount()
    {
        if (self::$sa !== null) {
            return self::$sa;
        }
        // 1) base64 do JSON (RECOMENDADO p/ .env — uma linha, sem aspas/quebras)
        $b64 = getenv('FIREBASE_SERVICE_ACCOUNT_B64');
        if ($b64) {
            $decoded = base64_decode(trim($b64), true);
            if ($decoded) {
                $parsed = json_decode($decoded, true);
                if (is_array($parsed)) {
                    return self::$sa = $parsed;
                }
            }
        }
        // 2) JSON cru no env
        $json = getenv('FIREBASE_SERVICE_ACCOUNT');
        if ($json) {
            $parsed = json_decode($json, true);
            if (is_array($parsed)) {
                return self::$sa = $parsed;
            }
        }
        // 3) arquivo
        $path = __DIR__ . '/../credentials/firebase-service-account.json';
        if (is_file($path)) {
            $parsed = json_decode((string) file_get_contents($path), true);
            if (is_array($parsed)) {
                return self::$sa = $parsed;
            }
        }
        return self::$sa = false;
    }

    public static function projectId()
    {
        $sa = self::serviceAccount();
        return $sa ? ($sa['project_id'] ?? null) : null;
    }

    public static function isConfigured()
    {
        $sa = self::serviceAccount();
        return $sa && !empty($sa['private_key']) && !empty($sa['client_email']) && !empty($sa['project_id']);
    }

    private static function b64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function getAccessToken()
    {
        if (self::$accessToken && time() < self::$accessTokenExp - 60) {
            return self::$accessToken;
        }
        $sa = self::serviceAccount();
        if (!$sa || empty($sa['private_key']) || empty($sa['client_email'])) {
            return null;
        }
        $now = time();
        $header = self::b64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claim = self::b64url(json_encode([
            'iss' => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));
        $signingInput = $header . '.' . $claim;
        $signature = '';
        if (!openssl_sign($signingInput, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256)) {
            return null;
        }
        $jwt = $signingInput . '.' . self::b64url($signature);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        $data = json_decode((string) $resp, true);
        if (empty($data['access_token'])) {
            return null;
        }
        self::$accessToken = $data['access_token'];
        self::$accessTokenExp = $now + (int) ($data['expires_in'] ?? 3600);
        return self::$accessToken;
    }

    /**
     * Envia DATA-ONLY (high priority) para um token FCM nativo.
     * @return array{ok:bool,code?:int,response?:string,error?:string}
     */
    public static function sendData($fcmToken, array $data)
    {
        if (!$fcmToken) {
            return ['ok' => false, 'error' => 'sem token'];
        }
        $projectId = self::projectId();
        $access = self::getAccessToken();
        if (!$access || !$projectId) {
            return ['ok' => false, 'error' => 'service account nao configurada'];
        }
        $dataStr = [];
        foreach ($data as $k => $v) {
            $dataStr[$k] = (string) $v; // FCM exige todos os valores string
        }
        $message = [
            'message' => [
                'token' => $fcmToken,
                // FCM v1 exige HIGH/NORMAL em maiúsculo. HIGH = acorda o app mesmo
                // em Doze/morto (necessário pro setBackgroundMessageHandler rodar).
                'android' => ['priority' => 'HIGH'],
                'data' => $dataStr,
            ],
        ];
        $ch = curl_init("https://fcm.googleapis.com/v1/projects/$projectId/messages:send");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $access,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($message),
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['ok' => $code >= 200 && $code < 300, 'code' => $code, 'response' => (string) $resp];
    }
}
