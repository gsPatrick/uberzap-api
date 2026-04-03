<?php
include("seguranca.php");
include "../classes/categorias_horarios.php";
$ch = new dinamico_horarios();
//entrada de formulário
$segunda = array(
	"manha_ini"=>$_POST['segunda_manha_ini'],
	"manha_fim"=>$_POST['segunda_manha_fim'],
	"noite_ini"=>$_POST['segunda_noite_ini'],
	"noite_fim"=>$_POST['segunda_noite_fim']
);
$terca = array(
	"manha_ini"=>$_POST['terca_manha_ini'],
	"manha_fim"=>$_POST['terca_manha_fim'],
	"noite_ini"=>$_POST['terca_noite_ini'],
	"noite_fim"=>$_POST['terca_noite_fim']
);
$quarta = array(
	"manha_ini"=>$_POST['quarta_manha_ini'],
	"manha_fim"=>$_POST['quarta_manha_fim'],
	"noite_ini"=>$_POST['quarta_noite_ini'],
	"noite_fim"=>$_POST['quarta_noite_fim']
);
$quinta = array(
	"manha_ini"=>$_POST['quinta_manha_ini'],
	"manha_fim"=>$_POST['quinta_manha_fim'],
	"noite_ini"=>$_POST['quinta_noite_ini'],
	"noite_fim"=>$_POST['quinta_noite_fim']
);
$sexta = array(
	"manha_ini"=>$_POST['sexta_manha_ini'],
	"manha_fim"=>$_POST['sexta_manha_fim'],
	"noite_ini"=>$_POST['sexta_noite_ini'],
	"noite_fim"=>$_POST['sexta_noite_fim']
);
$sabado = array(
	"manha_ini"=>$_POST['sabado_manha_ini'],
	"manha_fim"=>$_POST['sabado_manha_fim'],
	"noite_ini"=>$_POST['sabado_noite_ini'],
	"noite_fim"=>$_POST['sabado_noite_fim']
);
$domingo = array(
	"manha_ini"=>$_POST['domingo_manha_ini'],
	"manha_fim"=>$_POST['domingo_manha_fim'],
	"noite_ini"=>$_POST['domingo_noite_ini'],
	"noite_fim"=>$_POST['domingo_noite_fim']
);
$segunda = serialize($segunda);
$terca = serialize($terca);
$quarta = serialize($quarta);
$quinta = serialize($quinta);
$sexta = serialize($sexta);
$sabado = serialize($sabado);
$domingo = serialize($domingo);
$nome = $_POST['nome'];
$adicional = $_POST['adicional'];

$ch ->cad_dinamico_horario($cidade_id, $nome, $segunda, $terca, $quarta, $quinta, $sexta, $sabado, $domingo, $adicional);
echo "<script>alert('Cadastrado com sucesso!');window.location.href='dinamico_horario.php';</script>";
?>