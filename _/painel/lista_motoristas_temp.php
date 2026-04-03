<?php
include("seguranca.php");
include("../classes/motorista_docs.php");
include("../classes/motoristas.php");

$md = new motorista_docs();
$m = new motoristas();


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
      <hr>
      <div class="row">
        <div class="col-sm">
          <h4 class="page-header">Lista de Motoristas Aguardando Aprovação</h4>

          <!--Controlador de tamanho e margem da tabela-->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <th>ID:</th>
                <th>Nome</th>
                <th>Ações</th>
              </thead>
              <tbody>
                <?php
                $dados = $md->get_by_cidade($cidade_id);
                foreach ($dados as $linha) {
                  if($m ->get_motorista_cpf($linha['cpf'])){
                    continue;
                  }
                  echo '<tr>';
                  echo  '<td>' . $linha['id'] . '</td>';
                  echo  '<td>' . $linha['nome'] . '</td>';
                  //Ações                                      
                  echo  "<td>&nbsp<button type='button' class='btn btn-primary'  data-toggle='modal' data-target='#myModal$linha[id]'> Mostrar</button>
                                   &nbsp<a class='btn btn-info'  href='ver_docs.php?id=$linha[id]' role='button'>Documentos</a>
                                   &nbsp<a class='btn btn-success' href='aprovar_motorista.php?id=$linha[id]' role='button'>Aprovar</a>
                                  &nbsp<a class='btn btn btn-danger' href='deletar.php?id=$linha[id]&location=lista_motoristas_temp.php&categoria=motorista_docs' role='button' >Deletar</a>";

                  echo '</tr>';
                ?>

                  <!--Inicio Modal.-->
                  <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                          <center>
                            <h3 class="modal-title" id="myModalLabel"> Entregador ID: <?php echo $linha['id']; ?></h3>
                          </center>
                        </div>
                        <div class="modal-body">

                            <h4 style="text-align: center;"><img src="<?php echo IMG_DIR . $linha['img_selfie']; ?>" width="200px" height="auto"><br><br>
                            <b>Nome: </b><?php echo $linha['nome']; ?><br>
                            <b>Telefone: </b><?php echo $linha['telefone']; ?><br>
                            <b>CPF: </b><?php echo $linha['cpf']; ?><br>
                            <b>Veículo: </b><?php echo $linha['veiculo']; ?><br>
                            <b>Placa: </b><?php echo $linha['placa']; ?><br>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--fim modal-->
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div><!--Fechando container bootstrap-->
      <?php include("dep_query.php"); ?>
</body>

</html>