<?php
session_start();
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('America/Sao_Paulo'); //mude para o da sua regiao mais em https://www.php.net/manual/en/timezones.america.php
if(($_SESSION['email_usuario']== "") || ($_SESSION['senha_usuario']== "")){
    header("Location:index.php");
}
define ('IMG_DIR','../admin/uploads/');
$app_name = "Mobilidade";  //nome do aplicativo
$cidade_id = $_SESSION['cidade_id'];
?>