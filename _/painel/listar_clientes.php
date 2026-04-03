<?php
include("seguranca.php");
include("../classes/franqueados.php");
include("../classes/clientes.php");
$f = new franqueados();
$c = new clientes();
$lista_clientes = $c->get_clientes_cidade($cidade_id);
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
      <div class="row">
        <div class="col-md-6">
          <h4 class="page-header">Lista de Clientes</h4>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <th>ID</th>
              <th>Nome</th>
              <th>e-mail</th>
              <th>Telefone</th>
              <th>Senha</th>
              <th>Ações</th>
              <th>Status</th>
            </thead>
            <tbody>
              <?php
              foreach ($lista_clientes as $linha) {
                echo '<tr>';
                echo  '<td>' . $linha['id'] . '</td>';
                echo  '<td>' . $linha['nome'] . '</td>';
                echo  '<td>' . $linha['email'] . '</td>';
                echo  '<td>' . $linha['telefone'] . '</td>';
                echo  '<td>*****</td>'; // Exibe a senha como asteriscos
                // Ações                                      
                echo "<td>
                        <a href='excluir_cliente.php?id=$linha[id]' class='btn btn-danger' onclick='return confirm(\"Tem certeza que deseja excluir este cliente?\")'>
                          <i class='bi bi-trash'></i>
                        </a>&nbsp";
                echo "<button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
                echo "<button type='button' class='btn btn-warning' data-toggle='modal' data-target='#editar$linha[id]' data-whatever='{$linha['id']}' role='button'><i class='bi bi-pencil'></i></button>&nbsp";
                echo "</td>";
                if ($linha['ativo'] == '1') {
                  echo "<td><a class='btn btn-outline-success' href='desativar_cliente.php?id=$linha[id]&ativo=2' role='button'>Ativo</a>&nbsp</td>";
                } else {
                  echo "<td><a class='btn btn-outline-danger' href='desativar_cliente.php?id=$linha[id]&ativo=1' role='button'>Bloqueado</a>&nbsp</td>";
                }
                echo "</tr>";
              ?>
                <!--Inicio Modal.-->
                <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <center>
                          <h3 class="modal-title" id="myModalLabel"> Cliente: <?php echo $linha['nome']; ?></h3>
                        </center>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <br>
                        <b>Nome: </b><?php echo $linha['nome']; ?><br>
                        <b>email: </b><?php echo $linha['email']; ?><br>
                        <b>Telefone: </b><?php echo $linha['telefone']; ?><br>
                        <b>Saldo: </b><?php echo $linha['saldo']; ?><br>
                        <b>Senha: </b><?php echo '*****'; ?><br> <!-- Exibe a senha como asteriscos -->
                        <hr>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!--fim modal-->
                
                <!-- modal editar -->
<div class="modal fade" id="editar<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editar" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="edit_cliente.php?id=<?php echo $linha['id']; ?>" method="POST" enctype="multipart/form-data" name="upload">
        <div class="modal-header">
          <h5 class="modal-title" id="editar">Editar Cadastro <?php echo $linha['nome']; ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Nome -->
          <div class="row">
            <div class="form-group col-md-12">
              <label>Nome do cliente</label>
              <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="nome" required name="nome" placeholder="Nome do cliente" value="<?php echo $linha['nome']; ?>" />
            </div>
          </div>
          <!-- Email -->
          <div class="row">
            <div class="form-group col-md-12">
              <label>Email</label>
              <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="email" required name="email" placeholder="email" value="<?php echo $linha['email']; ?>" />
            </div>
          </div>
          <!-- Telefone -->
          <div class="row">
            <div class="form-group col-md-12">
              <label>Telefone:</label>
              <input class="form-control form-control-sm col-md-06 col-sm-06" id="telefone" type="number" required name="telefone" placeholder="ex: 066999395555" value="<?php echo $linha['telefone']; ?>" />
            </div>
          </div>
          <!-- Saldo -->
          <div class="row">
            <div class="form-group col-md-12">
              <label>Saldo:</label>
              <input class="form-control form-control-sm col-md-06 col-sm-06" id="saldo" type="number" required name="saldo" placeholder="ex: 50,00" value="<?php echo $linha['saldo']; ?>" />
            </div>
          </div>
          <!-- Senha -->
          <div class="row">
            <div class="form-group col-md-12">
              <label>Senha</label>
              <input class="form-control form-control-sm col-md-09 col-sm-09" type="password" id="senha" name="senha" placeholder="Senha" />
              <small>Deixe em branco se não desejar alterar a senha.</small>
            </div>
          </div>
          <!-- Checkbox para atualizar a senha -->
          <div class="form-group">
            <label>
              <input type="checkbox" name="update_password" value="on"> Atualizar Senha
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
      </form>
    </div>
  </div>
</div>

                <!--modal editar-->
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div><!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
</body>

</html>
