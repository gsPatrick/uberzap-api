<?php
Class seguranca {
    private $secret;
    public function __construct() {
        include '../bd/conexao.php';
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