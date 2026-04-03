<?
include("seguranca.php");
include_once "../bd/conexao.php";
include_once "../bd/config.php";
include_once("../classes/banners.php");
$b = new banners();

if (isset($_POST['btn_enviar'])) {
    $link = $_POST['link'];
    $ativa = isset($_POST['ativa']) ? 1 : 0;

    // Verifica se o arquivo foi enviado
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $img = uniqid('banner_', true) . '.' . $ext;
        $pasta='../admin/uploads/';
        move_uploaded_file($_FILES['img']['tmp_name'], $pasta . $img);

        // Cadastra o banner
        $b->cadastra($cidade_id, $img, $link, $ativa);
        echo "<script>alert('Banner cadastrado com sucesso!');</script>";
        //redirect para a mesma página para evitar reenvio de formulário
        echo "<script>window.location.href='banners.php';</script>";
    } else {
        echo "<script>alert('Erro ao enviar a imagem.');</script>";

    }
}

if(isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if ($b->deletar($delete_id)) {
        echo "<script>alert('Banner deletado com sucesso!');</script>";
        //redirect para a mesma página para evitar reenvio de formulário
        echo "<script>window.location.href='banners.php';</script>";
    } else {
        echo "<script>alert('Erro ao deletar o banner.');</script>";
    }
}


if (isset($_GET['toggle_id']) && isset($_GET['ativa'])) {
    $toggle_id = $_GET['toggle_id'];
    $ativa = $_GET['ativa'] == 1 ? 1 : 0;
    if ($b->alterarStatus($toggle_id, $ativa)) {
        echo "<script>alert('Status do banner atualizado com sucesso!');</script>";
        //redirect para a mesma página para evitar reenvio de formulário
        echo "<script>window.location.href='banners.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o status do banner.');</script>";
    }
}

$banners = $b->get_banners($cidade_id, false);
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
    <div id="cadastro" style="display: none;">
      <h4 class="page-header">CADASTRO DE BANNERS</h4>
      <hr>
        <form action="" method="POST" enctype="multipart/form-data" name="upload">
          <div class="row">
            <div class="form-group col-md-4">
              <label>Link para redirecionamento</label>
              <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="link" placeholder="https://" required />
            </div>
            <div class="form-group col-md-4">
              <label>Selecione abaixo para deixar o banner visível:</label><br>
              <input type=checkbox name="ativa" value="1"> Visível<br>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-8">
              <!--Realizando Upload de Imagem-->
              <label class="control-label">Imagem 600x200</label>
              <input class="form-control" type="file" required name="img">
            </div>
          </div>
          <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar">
        </form>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-6">
          <h4 class="page-header">LISTA DE BANNERS</h4>
        </div>
        <div class="col-md-3">
          <div class="form-group col-md-12">
            <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" id="pesquisa" name="pesquisa" placeholder="Pesquisar" />
          </div>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-primary btn-sm" style="float: right;" onclick="mostrar_cadastro()">Cadastrar</button>
        </div>
      </div>
      <hr>
    </div>
    <!--Controlador de tamanho e margem da tabela-->
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <th>Link</th>
          <th>Visível</th>
          <th>Ações</th>
        </thead>
        <tbody>
          <?php
          foreach ($banners as $linha) {
            echo '<tr>';
            $link = $linha['link'];
            $displayLink = (strlen($link) > 15) ? substr($link, 0, 15) . '...' : $link;
            echo '<td>' . htmlspecialchars($displayLink) . '</td>';
            if ($linha['ativo'] == 1) {
              echo "<td><a class='btn btn-outline-success' href='banners.php?toggle_id=$linha[id]&ativa=0' role='button'>Visível</a></td>";
            } else {
              echo "<td><a class='btn btn-outline-warning' href='banners.php?toggle_id=$linha[id]&ativa=1' role='button'>Invisível</a></td>";
            }
            //Ações                                      
            echo  "<td><button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
            echo "<a class='btn btn-danger' href='banners.php?delete_id=$linha[id]' role='button' onclick=\"return confirm('Tem certeza que deseja deletar este banner?');\"><i class='bi bi-trash3'></i></a>&nbsp</td>";
            echo "</tr>";
          ?>
            <!--Inicio Modal.-->
            <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel<?php echo $linha['id']; ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="myModalLabel<?php echo $linha['id']; ?>">
                      <i class="bi bi-image"></i> Banner #<?php echo $linha['id']; ?>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body text-center">
                    <img src="<?php echo htmlspecialchars(URL_IMAGEM . $linha['img']); ?>" class="img-fluid rounded shadow mb-3" style="max-width: 620px; max-height: 350px;" alt="Banner">
                    <div class="mt-3 text-left">
                      <p><strong>Link:</strong> <a href="<?php echo htmlspecialchars($linha['link']); ?>" target="_blank"><?php echo htmlspecialchars($linha['link']); ?></a></p>
                      <p>
                        <strong>Visível:</strong>
                        <?php echo $linha['ativo'] == 1 ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>'; ?>
                      </p>
                      <?php if (!empty($linha['dt_insercao'])): ?>
                        <p><strong>Data de inserção:</strong> <?php echo htmlspecialchars($linha['dt_insercao']); ?></p>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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
  <!--Fechando container bootstrap-->
  <?php include("dep_query.php"); ?>
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script>
    function mostrar_cadastro() {
      let cadastro = document.getElementById("cadastro");
      if (cadastro.style.display == "none") {
        cadastro.style.display = "block";
      } else {
        cadastro.style.display = "none";
      }
    }
    $('#pesquisa').keyup(function() {
      var nomeFiltro = $(this).val().toLowerCase();
      $('table tbody').find('tr').each(function() {
        var conteudoCelula = $(this).find('td:first').text();
        var corresponde = conteudoCelula.toLowerCase().indexOf(nomeFiltro) >= 0;
        $(this).css('display', corresponde ? '' : 'none');
      });
    });
  </script>
</body>

</html>