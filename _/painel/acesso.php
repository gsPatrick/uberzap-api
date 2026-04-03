<?php
include_once '../classes/franqueados.php'; 
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$l = new franqueados();
//login
if($l->login($usuario, $senha)){
    session_start();
    $_SESSION['email_usuario'] = $usuario;
    $_SESSION['senha_usuario'] = $senha;
    $_SESSION['id_usuario'] = $l->get_user_id($usuario);
    $_SESSION['cidade_id'] = $l->login($usuario, $senha)['cidade_id'];
    header("Location: dash.php");
} else {
    echo '<script>alert("Usuário ou senha incorretos!");</script>';
    echo '<script>window.location.href="../painel/index.php";</script>';
}