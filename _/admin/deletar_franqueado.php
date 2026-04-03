<?php
include("seguranca.php");
include_once '../classes/franqueados.php';
$id= $_GET['id'];
$u = new franqueados();
$u -> delet_usuario($id);
echo '<script>alert("Usuário deletado com sucesso!");</script>';
echo '<script>window.location.href="franqueados.php";</script>';
?>