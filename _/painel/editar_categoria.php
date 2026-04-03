<?
include("seguranca.php");
include "../classes/categorias.php";
include "../classes/categorias_horarios.php";
include "../classes/dinamico_mapa.php";
$c  = new Categorias();
$dh = new dinamico_horarios();
$dm = new dinamico_mapa();

$dinamicos_horarios = $dh->get_dinamico_horarios_loja($cidade_id);
$dinamicos_mapa     = $dm->get_dinamico_mapa($cidade_id);
$dados = $c->get_categoria($_GET['id']);
?><!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php");
?>

<body>
  <div class="container-fluid">
    <div class="container">
      <div class="container-principal-produtos">
        <h4 class="page-header">EDITAR CATEGORIA</h4>
        <hr>
        <form action="edit_cat.php?id=<?php echo $_GET['id'];?>" method="POST" enctype="multipart/form-data" name="upload">
        <div class="row">
              <div class="form-group col-md-4">
                <label>Nome da Categoria:</label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="nome" placeholder="Digite o nome da categoria" required  value="<?php echo $dados[0]['nome']; ?>"/>
              </div>
              <div class="form-group col-md-4">
                <label>Selecione abaixo para deixar a categoria visível:</label>
                <input type=checkbox name="ativa" value="1" <?php if($dados[0]['ativa'] == 1){ echo "checked"; } ?>> Visível<br>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Imagem</label>
                <input class="form-control" type="file" name="img">
              </div>
              <div class="form-group col-md-4">
                <label>Descrição da Categoria:</label>
                <textarea cols="4" rows="2" class="form-control col-md-12" name="descricao" placeholder="Digite uma breve descrição sobre a categoria"><?php echo $dados[0]['descricao']; ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Valor da taxa por KM rodado: </label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="tx_km" placeholder="Ex: 1,00" value="<?php echo $dados[0]['tx_km']; ?>" />
              </div>
              <div class="form-group col-md-4">
                <label>Valor da taxa adicional por minuto de corrida: </label>&nbsp
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="tx_minuto" placeholder="Ex: 0,80" value="<?php echo $dados[0]['tx_minuto']; ?>" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Valor da taxa base: </label>&nbsp
                <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_base" placeholder="Ex: 5,00" value="<?php echo $dados[0]['tx_base']; ?>" />
              </div>
              <div class="form-group col-md-4">
                <label>Valor da taxa mínima: </label>&nbsp
                <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_minima" placeholder="Ex: 5,00" value="<?php echo $dados[0]['tx_minima']; ?>" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Raio de chamado entre ponto de partida e motorista em metros: </label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="raio" placeholder="Ex: 5000" value="<?php echo $dados[0]['raio']; ?>" />
              </div>
              <div class="form-group col-md-4">
                <label>Ordem de exibição: </label><br>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="ordem" placeholder="Ex: 1" value="<?php echo $dados[0]['ordem']; ?>" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Dinâmico de Horários a aplicar:</label>
                <div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
                  <?php foreach ($dinamicos_horarios as $linha) { ?>
                    <input type=checkbox <? if(in_array($linha['id'], $dados[0]['dinamico_horarios'])){ echo "checked"; } ?>
                     name="dinamico_horarios[]" value="<?php echo $linha['id']; ?>"> <?php echo $linha['nome']; ?><br>
                  <?php } ?>
                </div>
                <br>
              </div>
              <div class="form-group col-md-4">
                <label>Dinâmico de Local a aplicar:</label>
                <div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
                  <?php foreach ($dinamicos_mapa as $linha) { ?>
                    <input type=checkbox <? if(in_array($linha['id'], $dados[0]['dinamico_local'])){ echo "checked"; } ?>
                    name="dinamico_local[]" value="<?php echo $linha['id']; ?>"> <?php echo $linha['nome']; ?><br>
                  <?php } ?>
                </div>
                <br>
              </div>
            </div>
          <input type="submit" class="btn btn-primary" name="btn_enviar" value="Salvar">
        </form>
        <br>
    </div>
  </div>
  </div>
  <!--Fechando container bootstrap-->
  <?php include("dep_query.php"); ?>
  <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function() {
      $('#input_img').change(function() {
        var file = $(this)[0].files[0];
        var fileReader = new FileReader();
        fileReader.onloadend = function() {
          $('#img').attr('src', fileReader.result);
        }
        fileReader.readAsDataURL(file);
      });
    });
  </script>
</body>

</html>