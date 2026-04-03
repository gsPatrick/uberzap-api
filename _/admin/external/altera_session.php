<?php
include "../seguranca.php";
$_SESSION['cidade_id']=$_GET['id'];
$redirect = $_GET['redirect'];
echo '<script>alert("Logado com sucesso!");</script>';
echo '<script>window.location.href="../'.$redirect.'";</script>';
?>