<?php
/**
 * Webhook do BOT/IA (automate.uberzap.app.br): notifica o passageiro a cada
 * mudança de status da corrida. A IA usa isso pra mandar a mensagem no WhatsApp.
 *
 * 1 POST por mudança de status:
 *   accepted  -> user_id, nome_motorista, veiculo, placa
 *   arrived   -> user_id
 *   started   -> user_id
 *   completed -> user_id, valor
 *
 * user_id = user_whatsapp da corrida (ex.: 247227343757558@lid). Se a corrida
 * não veio da IA (sem user_whatsapp), não envia nada.
 */
require_once __DIR__ . '/uzlog.php';

class BotWebhook
{
    const URL = 'https://automate.uberzap.app.br/webhook/atualizacao-corrida';

    /**
     * @param array  $corrida linha da corrida (precisa de user_whatsapp e id)
     * @param string $status  accepted|arrived|started|completed
     * @param array  $extra   campos adicionais (nome_motorista, placa, valor...)
     * @return int HTTP code (0 = não enviou)
     */
    public static function notificarPassageiro($corrida, $status, $extra = [])
    {
        $userId = trim((string) ($corrida['user_whatsapp'] ?? ''));
        if ($userId === '') {
            return 0; // corrida sem WhatsApp do passageiro (não veio da IA)
        }

        $payload = array_merge([
            'user_id' => $userId,
            'status'  => $status,
        ], $extra);
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $rideId = $corrida['id'] ?? '?';

        // Retry: até 3 tentativas se não voltar 2xx (cobre timeout/instabilidade
        // do n8n) — foi o que causava "deixou de notificar" em alguns.
        $code = 0;
        $resp = '';
        for ($tent = 1; $tent <= 3; $tent++) {
            try {
                $ch = curl_init(self::URL);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_TIMEOUT => 12,
                ]);
                $resp = curl_exec($ch);
                $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err = curl_error($ch);
                curl_close($ch);
            } catch (\Throwable $e) {
                $code = 0;
                $err = $e->getMessage();
            }
            uzlog('[bot-webhook] corrida #' . $rideId . " status=$status tent=$tent http=$code " . ($code ? substr((string) $resp, 0, 100) : ('ERRO ' . ($err ?? ''))));
            if ($code >= 200 && $code < 300) {
                break; // sucesso
            }
            if ($tent < 3) {
                usleep(700000); // 0.7s antes de tentar de novo
            }
        }
        return $code;
    }
}
