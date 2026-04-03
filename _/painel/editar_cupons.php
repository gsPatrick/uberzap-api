<?php
include("seguranca.php");
include_once "../classes/cupons.php";
include_once "../classes/tempo.php";
$c = new cupons();
$t = new tempo();
$dados = $c->get_cupon($_GET['id']);
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
        <div class="container">
            <div class="container-principal-produtos">
                <h4 class="page-header">EDITAR CUPON</h4>
                <hr>
                <form action="edit_cupon.php?id=<?php echo $_GET['id']; ?>" method="POST" enctype="multipart/form-data" name="upload">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Código do cupom:</label>
                            <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="nome" placeholder="Ex: #desconto" required value="<?php echo $dados['nome']; ?>" />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Valor mínimo:</label>
                            <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="valor_min" placeholder="Ex: 30,00" required value="<?php echo $dados['valor_min']; ?>" />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Primeira corrida:</label>
                            <select class="form-control form-control-sm col-md-03 col-sm-03" name="primeira_compra">
                                <option value="1" <?php if ($dados['primeira_compra'] == 1) {
                                                        echo "selected";
                                                    } ?>>Sim</option>
                                <option value="0" <?php if ($dados['primeira_compra'] == 0) {
                                                        echo "selected";
                                                    } ?>>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Válido até:</label>
                            <br>
                            <input type="date" id="datepicker" name="validade" value="<?php echo $t->data($dados['validade']); ?>" />
                            <input type="time" id="time" name="hora" value="<?php echo $t->hora_mysql_para_user($dados['validade']); ?>" />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Quantidade:</label>
                            <input class="form-control form-control-sm col-md-03 col-sm-03" type="number" name="quantidade" placeholder="Ex: 10" required value="<?php echo $dados['quantidade']; ?>" />
                        </div>
                        <div class="form-group col-md-3">
                            <label>Um por usuário?</label>
                            <select class="form-control form-control-sm col-md-03 col-sm-03" name="uso_unico">
                                <option value="1" <?php if ($dados['uso_unico'] == 1) {
                                                        echo "selected";
                                                    } ?>>Sim</option>
                                <option value="0" <?php if ($dados['uso_unico'] == 0) {
                                                        echo "selected";
                                                    } ?>>Não</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label>Forma do desconto:</label>
                            <select class="form-control form-control-sm col-md-03 col-sm-03" id="tipo_desconto" name="tipo_desconto">
                                <option value="1" <?php if ($dados['tipo_desconto'] == 1) {
                                                        echo "selected";
                                                    } ?>>Porcentagem</option>
                                <option value="2" <?php if ($dados['tipo_desconto'] == 2) {
                                                        echo "selected";
                                                    } ?>>Valor fixo</option>
                            </select>
                        </div>
                        <div id="div-porcentagem" class="form-group col-md-3" style="display: <?php if ($dados['tipo_desconto'] == 1) {echo "block";} else { echo "none"; } ?>;">
                            <label>Desconto em %:</label>
                            <input class="form-control form-control-sm col-md-03 col-sm-03" type="number" name="valor_porcentagem" placeholder="Ex: 10" value="<?php echo $dados['valor']; ?>" />
                        </div>
                        <div id="div-valor" class="form-group col-md-3" style="display: <?php if ($dados['tipo_desconto'] == 2) {echo "block";} else { echo "none"; } ?>;">
                            <label>Desconto em R$:</label>
                            <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="valor_reais" placeholder="Ex: 7,00" value="<?php echo $dados['valor']; ?>" />
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary" name="btn_enviar" value="Salvar " />
                </form>
            </div>
        </div>
    </div>
    <!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <script>
        $("#tipo_desconto").on("change", function() {

            if ($(this).val() == 1) {
                $("#div-porcentagem").show();
                $("#div-valor").hide();
            } else if ($(this).val() == 2) {
                $("#div-porcentagem").hide();
                $("#div-valor").show();
            } else {
                $("#div-porcentagem").hide();
                $("#div-valor").hide();
            }
        });
    </script>

</body>

</html>