<?php
Class seguranca {
    private $secret;
    public function __construct() {
        require_once __DIR__ . '/../bd/conexao.php';
        global $secret;
        $this->secret = $secret;
    }

    public function compare_secret($secret) {
        if ($secret == $this->secret) {
            return true;
        } else {
            return false;
        }
    }
}