<?php
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('America/Campo_Grande'); //mude para o da sua regiao
if(($_SESSION['email_usuario']== "") || ($_SESSION['senha_usuario']== "")){
    header("Location:index.php");
    exit;
}
$cidade_id = $_SESSION['cidade_id'];
$admin = $_SESSION['admin'];
?>