<?php 
include "seguranca.php";
include "../classes/categorias_horarios.php";
$ch = new dinamico_horarios();
$ch->del_dinamico_horarios($_GET['id']);
echo "<script>window.location.href='dinamico_horario.php';</script>";
?>