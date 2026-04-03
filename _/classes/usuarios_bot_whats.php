<?php
Class usuarios_bot_whats {
    private $pdo;
    private $conexao;
    public function __construct() {
        include '../bd/conexao.php';
        $this->conexao = $pdo;
    }

    public function insere($user_id, $msg_recebida, $msg_enviada){
        $sql = $this->conexao->prepare("INSERT INTO usuarios_bot_whats (user_id, msg_recebida, msg_enviada) VALUES (:user_id, :msg_recebida, :msg_enviada)");
        $sql->bindValue(":user_id", $user_id);
        $sql->bindValue(":msg_recebida", $msg_recebida);
        $sql->bindValue(":msg_enviada", $msg_enviada);
        $sql->execute();
        return $this->conexao->lastInsertId();
    }

    public function get_msgs($user_id){
        $sql = $this->conexao->prepare("SELECT * FROM usuarios_bot_whats WHERE user_id = :user_id");
        $sql->bindValue(":user_id", $user_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function get_msg($id){
        $sql = $this->conexao->prepare("SELECT * FROM usuarios_bot_whats WHERE id = :id");
        $sql->bindValue(":id", $id);
        $sql->execute();
        return $sql->fetch();
    }

    public function get_last_msg($user_id){
        $sql = $this->conexao->prepare("SELECT * FROM usuarios_bot_whats WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
        $sql->bindValue(":user_id", $user_id);
        $sql->execute();
        return $sql->fetch();
    }

    public function getByUserId($user_id){
        $sql = $this->conexao->prepare("SELECT * FROM usuarios_bot_whats WHERE user_id = :user_id");
        $sql->bindValue(":user_id", $user_id);
        $sql->execute();
        $result = $sql->fetchAll();
        return !empty($result) ? $result : false;
    }

    public function limpaMensagens($path, $user_id) {
        //requisição post para n8n.rothen.com.br/webhook-test/$path
        $url = "https://n8n.mestredosaplicativos.com.br/webhook/$path";
        $data = array('user_id' => $user_id);
        
        // Initialize cURL session
        $ch = curl_init($url);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        
        // Make sure content length is calculated correctly
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Execute cURL session and get the response
        $result = curl_exec($ch);
        
        // Check for errors
        if(curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("cURL Error: " . $error);
        }
        
        // Close cURL session
        curl_close($ch);
        
        return $result;
    
    }

}