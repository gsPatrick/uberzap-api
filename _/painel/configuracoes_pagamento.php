<?
include("seguranca.php");
include "../classes/configuracoes_pagamento.php";
$c = new configuracoes_pagamento();
$dados = $c->read_configuracoes_pagamento($cidade_id);
if(isset($dados['token'])){
    $token = $dados['token']; 
}else{
    $token = '';
}
?>
<!doctype html>
<html lang="pt-br">
<?php include("head.php"); ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">

    </div>
    <div class="container">
        <div class="container-principal-produtos">
            <h4 class="page-header">CONFIGURAÇÕES DE PAGAMENTO</h4>
            <hr>
            <div class="col-md-8">
                <form action="cad_configuracoes_pagamento.php" method="POST" enctype="multipart/form-data" name="upload">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Token para Pagamento Online Mercado Pago:</label>
                            <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="token" placeholder="Token" value="<?php echo $token; ?>">
                            <a href="https://api.pagmp.com/" target="_blank">Clique aqui para gerar o token na api.pagmp.com</a>
                        </div>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-primary" name="btn_enviar" value="Salvar">
                </form>
            </div>

        </div>

    </div>

    </div>
    <br>
    </div>
    <!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
    <script>

    </script>
</body>