<?php
$user=$_POST['usuario'];
$senha=$_POST['senha'];

include '../classes/admin.php';
$admin = new admin();
if($admin->login($user, $senha)){
    session_start();
    $_SESSION['email_usuario'] = $user;
    $_SESSION['senha_usuario'] = $senha;
    $_SESSION['id_usuario'] = $admin->get_user_id($user);
    $_SESSION['cidade_id'] = $admin->get_cidade_id($admin->get_user_id($user));
    $_SESSION['admin'] = $admin->get_admin($admin->get_user_id($user));
    header("Location: ../admin/dash.php");
} else {
    echo '<script>alert("Usuário ou senha incorretos!");</script>';
    echo '<script>window.location.href="../admin/index.php";</script>';
}
?>