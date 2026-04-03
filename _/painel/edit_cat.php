<?php 
include "seguranca.php";
include_once '../classes/categorias.php';
include_once '../classes/upload.php';
$c = new categorias();
$id = $_GET['id'];
$nome = $_POST['nome'];
$ativa = $_POST['ativa'];
$descricao = $_POST['descricao'];
$tx_km = $_POST['tx_km'];
$tx_minuto = $_POST['tx_minuto'];
$tx_base = $_POST['tx_base'];
$tx_minima = $_POST['tx_minima'];
$raio = $_POST['raio'];
$ordem = $_POST['ordem'];
$loja_id = $_SESSION['loja_id'];
$ordem = $_POST['ordem'];
$dinamico_horarios = isset($_POST['dinamico_horarios']) ? $_POST['dinamico_horarios'] : array();
$dinamico_local = isset($_POST['dinamico_local']) ? $_POST['dinamico_local'] : array();

$dados = $c->get_categoria($id);


//parametros para imagem
$pasta='../admin/uploads/';
$img=$_FILES['img'];
if($img['name'] != ""){
    $upload = new Upload($img, 800, 800, $pasta);
    $nome_img = $upload->salvar();
}else{
    $nome_img = $dados[0]['img'];
}

$cadastro = $c->edit_categoria($id, $cidade_id, $nome, $descricao, $nome_img, $tx_km, $tx_minuto, $tx_minima, $tx_base, $raio, $dinamico_horarios, $dinamico_local, $ativa, $ordem);
if($cadastro){
    echo "<script>alert('Categoria editada com sucesso!');window.location.href='../painel/categorias.php'</script>";
}else{
    echo "<script>alert('Erro ao editar categoria!');window.location.href='../painel/categorias.php'</script>";
}





?>