<?php
include("seguranca.php");
include("../classes/preco_setores.php");

$ps = new preco_setores();

$id_setor_a = $_POST['setor_a'];
$id_setor_b = $_POST['setor_b'];
$preco = $_POST['preco'];
$cidade_id = $_SESSION['cidade_id'];

if ($ps->cadastra($id_setor_a, $id_setor_b, $preco, $cidade_id)) {
    echo "<script>location.href='preco_setores.php';</script>";
} else {
    echo "location.href='preco_setores.php';</script>";
}
?>