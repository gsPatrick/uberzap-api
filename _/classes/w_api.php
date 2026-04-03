<?php
Class w_api{
    //recebe o token no construtor
    private $token;
    private $api_id;
    private $url = 'https://api.w-api.app';
    public function __construct($token, $api_id){
        $this->token = $token;
        $this->api_id = $api_id;
    }

    /**
     * Get QR code for connection
     * 
     * @param bool $asImage Whether to return as image (true) or JSON (false)
     * @return array Response from the API containing QR code data
     */
    public function getQrCode($asImage = true) {
        $imageParam = $asImage ? 'enable' : 'disable';
        $url = "{$this->url}/v1/instance/qr-code?instanceId={$this->api_id}&image={$imageParam}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($asImage && $httpCode == 200) {
            // If requesting image and the request was successful
            header('Content-Type: image/png');
            echo $response;
            exit;
        } else {
            // Return JSON response for non-image requests or errors
            return [
            'status' => $httpCode,
            'response' => json_decode($response, true)
            ];
        }
    }

    /**
     * Send a text message
     * 
     * @param string $phone Recipient phone number with country code
     * @param string $message Text message to send
     * @param int $delayMessage Optional delay in seconds
     * @return array Response from the API
     */
    public function enviarMensagem($phone, $message, $delayMessage = null) {
        $url = "{$this->url}/v1/message/send-text?instanceId={$this->api_id}";
        
        $data = [
            'phone' => $phone,
            'message' => $message
        ];
        
        if ($delayMessage !== null) {
            $data['delayMessage'] = $delayMessage;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->token}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    /**
     * Send a link message
     * 
     * @param string $phone Recipient phone number with country code
     * @param string $message Text message to send
     * @param string $linkUrl URL to be included in the message
     * @param string $title Title of the link preview
     * @param string $linkDescription Description of the link
     * @param string $image URL of the image for link preview
     * @param int $delayMessage Optional delay in seconds
     * @return array Response from the API
     */
    public function enviarLink($phone, $message, $linkUrl, $title, $linkDescription, $image = null, $delayMessage = null) {
        $url = "{$this->url}/v1/message/send-link?instanceId={$this->api_id}";
        
        $data = [
            'phone' => $phone,
            'message' => $message,
            'linkUrl' => $linkUrl,
            'title' => $title,
            'linkDescription' => $linkDescription
        ];
        
        if ($image !== null) {
            $data['image'] = $image;
        }
        
        if ($delayMessage !== null) {
            $data['delayMessage'] = $delayMessage;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->token}"
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}



?>