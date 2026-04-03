<?
include("seguranca.php");
include "../classes/categorias.php";
$c = new categorias();
$categorias = $c->get_categorias($cidade_id);
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
  <div class="container-fluid">
  </div>
  <div class="container">
    <div class="container-principal-produtos">
      <h4 class="page-header">CADASTRO DE INFORMAÇÕES DO MOTORISTA</h4>
      <hr>
      <form action="cad_motorista.php" method="POST" enctype="multipart/form-data" name="upload">
        <div class="row">
          <div class="form-group col-md-4">
            <label>Nome do Motorista:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="nome" placeholder="Nome do Motorista" />

          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-8">
            <!--Realizando Upload de Imagem-->
            <label class="control-label">Foto do Motorista (formato jpg ou jpeg)</label>
            <input class="form-control" type="file" required name="img">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-8">
             <label>e-mail:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="email" placeholder="e-mail" />
            </br>
            <label>Telefone:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="fone" placeholder="ex: 5511999887766" />
            </br>
            <label>CPF do Motorista: (somente números)</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="cpf" required name="cpf" placeholder="CPF (apenas números)" />
            <br>
            <label>Senha para acesso do Motorista:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="senha" placeholder="Senha" />
            <br>
            <label>Veículo do Motorista:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="veiculo" placeholder="Ex: CG 150 2012" />
            <br>
            <label>Placa do veículo:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="placa" placeholder="Placa" />
            <br>
            <label>Taxa a cobrar em porcentagem:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="taxa" placeholder="Ex: 10 (somente números)" />
            <br>
            <label>Iniciar com Saldo:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="saldo" placeholder="Ex: 50,00" />
            <br>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Categorias:</label>
                <div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
                  <?php
                  foreach ($categorias as $key => $value) {
                    echo '<input type="checkbox" name="ids_categorias[]" value="' . $value['id'] . '"> ' . $value['nome'] . '<br>';
                  }
                  ?>
                </div>
                <br>
              </div>
            </div>
            <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar Motorista">
            <hr>
      </form>
    </div>
  </div><!--Fechando container bootstrap-->
  <?php include("dep_query.php"); ?>
</body>

</html>
<script>
  //deixa somente numeros no cpf
  $("#cpf").keyup(function() {
    var v = $(this).val();
    v = v.replace(/\D/g, "") //permite digitar apenas números
    $(this).val(v);
  });
</script>