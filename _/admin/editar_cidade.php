<?php
include("seguranca.php");
include_once("../classes/cidades.php");

$c = new cidades();
$id = $_GET['id'];
$dados = $c->get_dados_cidade($id);

?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
        <div class="container">
            <div class="container-principal-produtos">
                <div id="edit">
                    <h4 class="page-header">Edição de Cidade/Estado</h4>
                    <hr>
                    <form action="editar_cidade.php" method="POST" enctype="multipart/form-data" name="upload">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <label>Nome da Cidade / Estado:</label><br>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="nome" value="<?php echo $dados['nome']; ?>" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="latitude" value="<?php echo $dados['latitude']; ?>" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="longitude" value="<?php echo $dados['longitude']; ?>" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <input type="submit" class="btn btn-primary" name="btn_enviar" value="Editar">
                            </div>
                        </div>
                    </form>
                </div>
                <hr>
            </div>
        </div>
    </div>
</body>
<?php 
if(isset($_POST['btn_enviar'])){
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $c->editar($id, $nome, $latitude, $longitude);
    echo "<script>window.location.href = 'cidades.php';</script>";
}