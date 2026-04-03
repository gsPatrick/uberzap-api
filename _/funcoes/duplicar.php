<?php 
include_once "../classes/duplicar.php";
$duplicar = new duplicar();
$id = $_GET['id'];
$categoria = $_GET['categoria'];
$duplicar->duplicar($id, $categoria);
echo "<script>window.history.go(-1);</script>";
?> 