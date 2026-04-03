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
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php");
?>

<body>
  <div class="container-fluid">
    <div class="container">
      <div class="container-principal-produtos">
        <div id="cadastro" style="display: none;">
          <h4 class="page-header">CADASTRO DE CATEGORIAS</h4>
          <hr>
          <form action="cad_categorias.php" method="POST" enctype="multipart/form-data" name="upload">
            <div class="row">
              <div class="form-group col-md-4">
                <label>Nome da Categoria:</label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="nome" placeholder="Digite o nome da categoria" required />
              </div>
              <div class="form-group col-md-4">
                <label>Selecione abaixo para deixar a categoria visível:</label>
                <input type=checkbox name="ativa" value="1"> Visível<br>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Imagem</label>
                <input class="form-control" type="file" required name="img">
              </div>
              <div class="form-group col-md-4">
                <label>Descrição da Categoria:</label>
                <textarea cols="4" rows="2" class="form-control col-md-12" name="descricao" placeholder="Digite uma breve descrição sobre a categoria"></textarea>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Valor da taxa por KM rodado: </label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="tx_km" placeholder="Ex: 1,00" />
              </div>
              <div class="form-group col-md-4">
                <label>Valor da taxa adicional por minuto de corrida: </label>&nbsp
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="tx_minuto" placeholder="Ex: 0,80" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Valor da taxa base: </label>&nbsp
                <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_base" placeholder="Ex: 5,00" />
              </div>
              <div class="form-group col-md-4">
                <label>Valor da taxa mínima: </label>&nbsp
                <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_minima" placeholder="Ex: 5,00" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Raio de chamado entre ponto de partida e motorista em metros: </label>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="raio" placeholder="Ex: 5000" />
              </div>
              <div class="form-group col-md-4">
                <label>Ordem de exibição: </label><br>
                <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" name="ordem" placeholder="Ex: 1" />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label>Dinâmico de Horários a aplicar:</label>
                <div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
                  <?php foreach ($dinamicos_horarios as $linha) { ?>
                    <input type=checkbox name="dinamico_horarios[]" value="<?php echo $linha['id']; ?>"> <?php echo $linha['nome']; ?><br>
                  <?php } ?>
                </div>
                <br>
              </div>
              <div class="form-group col-md-4">
                <label>Dinâmico de Local a aplicar:</label>
                <div style="border: solid; width: 100%; padding-left: 10px;  height: 100px; background-color: #f5f5f5; border-color: gray; border-radius: 5px; overflow-y: scroll;">
                  <?php foreach ($dinamicos_mapa as $linha) { ?>
                    <input type=checkbox name="dinamico_local[]" value="<?php echo $linha['id']; ?>"> <?php echo $linha['nome']; ?><br>
                  <?php } ?>
                </div>
                <br>
              </div>
            </div>
              <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar">
          </form>
          <hr>
        </div>
      </div>
    <!--Controlador de tamanho e margem da tabela-->
    <div class="row">
      <div class="col-md-6">
        <h4 class="page-header">LISTA DE CATEGORIAS</h4>
      </div>
      <div class="col-md-6">
        <button type="button" class="btn btn-primary btn-sm" style="float: right;" onclick="mostrar_cadastro()">Cadastrar</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <th>ID</th>
          <th>Título</th>
          <th>Ativa</th>
          <th>Ações</th>
        </thead>
        <tbody>
          <?php

          foreach ($c->get_categorias($cidade_id, false) as $linha) {
            echo '<tr>';
            echo  '<td>' . $linha['id'] . '</td>';
            echo  '<td>' . $linha['nome'] . '</td>';
            if ($linha['ativa'] == 1) {
              echo  '<td>Sim</td>';
            } else {
              echo  '<td><font color="#FF2300">Não</td>';
            }
            //Ações                                      
            echo  "<td><button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
            echo "<a class='btn btn-warning' href='editar_categoria.php?id=$linha[id]' role='button'><i class='bi bi-pencil'></i></a>&nbsp";
            echo "<a class='btn btn-info' href='../funcoes/duplicar.php?id=$linha[id]&categoria=categorias' role='button'><i class='bi bi-arrows-angle-expand'></i></a>&nbsp";
            echo "<a class='btn btn btn-danger' onclick='deletar(event, {$linha['id']})'  role='button'><i class='bi bi-trash'></i></a>&nbsp</td>";
            echo "</tr>";
          ?>
            <!--Inicio Modal.-->
            <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <center>
                      <h3 class="modal-title" id="myModalLabel"> Categoria id: <?php echo $linha['id']; ?></h3>
                    </center>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  </div>
                  <div class="modal-body">
                    <h4>
                      <div style="text-align: center;">
                      <img src="<?php echo IMG_DIR . $linha['img']; ?>" width="150px" height="20%"><br><br>
                      </div>
                      <b>Nome: </b><?php echo $linha['nome']; ?><br>
                      <b>Descrição: </b><?php echo $linha['descricao']; ?><br>
                      <b>Taxa por KM: </b><?php echo $linha['tx_km']; ?><br>
                      <b>Taxa por minuto: </b><?php echo $linha['tx_minuto']; ?><br>
                      <b>Taxa base: </b><?php echo $linha['tx_base']; ?><br>
                      <b>Taxa mínima: </b><?php echo $linha['tx_minima']; ?><br>
                      <hr>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                  </div>
                </div>
              </div>
            </div>
            <!--fim modal-->
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  </div>
  </div>
  </div>
  <!--Fechando container bootstrap-->
  <?php include("dep_query.php"); ?>
  <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
  <script>
    let em_cadastro = false;
    if (localStorage.getItem('em_cadastro') == 'true') {
      mostrar_cadastro();
    }

    function deletar(event, id) {
      event.preventDefault();
      if (confirm("Deseja realmente excluir?")) {
        window.location.href = 'deletar.php?id=' + id + '&categoria=categorias&location=categorias.php';
      }
    }

    function mostrar_cadastro() {

      let cadastro = document.getElementById("cadastro");
      if (cadastro.style.display == "none") {
        localStorage.setItem('em_cadastro', true);
        $("#cadastro").animate({
          opacity: 1,
          left: "+=50",
          height: "toggle"
        }, 400, function() {
          // Animation complete.
        });
      } else {
        localStorage.setItem('em_cadastro', false);
        $("#cadastro").animate({
          opacity: 1,
          left: "+=50",
          height: "toggle"
        }, 400, function() {
          // Animation complete.
        });
      }
    }
  </script>
</body>

</html>