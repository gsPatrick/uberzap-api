<?php 
include $_SERVER['DOCUMENT_ROOT'] . "/_/bd/conexao.php";
$identificador = $_POST['identificador'];
$busca = mysqli_query($conexao, "SELECT * FROM lojas WHERE identificador = '$identificador'");
if (mysqli_num_rows($busca) > 0) {
    echo 1;
} else {
    echo 0;
}