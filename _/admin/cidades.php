<?php
include("seguranca.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/_/bd/conexao.php";
if (isset($_GET['redirect'])) {
  $redirect = $_GET['redirect'];
} else {
  $redirect = "cidades.php";
}
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
  <div class="container-fluid">
    <div class="container">
      <div class="container-principal-produtos">
        <div id="cadastro" style="display: none;">
          <h4 class="page-header">Cadastro de Cidade/Estado</h4>
          <hr>
          <form action="cad_cidade.php" method="POST" enctype="multipart/form-data" name="upload">
            <label>Nome da Cidade / Estado:</label><br>
            <div class="row">
              <div class="form-group col-md-4">
                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="nome" placeholder="Digite o nome da cidade" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="latitude" placeholder="Latitude" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="longitude" placeholder="Longitude" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="chave" placeholder="Chave Cadastro" required />
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar">
              </div>
            </div>
          </form>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-9">
            <h4 class="page-header">Lista de Cidades</h4>
          </div>
          <div class="col-md-3">
            <button type="button" class="btn btn-primary btn-sm" style="float: right;" onclick="mostrar_cadastro()">Cadastrar</button>
          </div>
        </div>

        <hr>
        <!--Controlador de tamanho e margem da tabela-->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <th>#</th>
              <th>Nome</th>
              <th>Ações</th>
            </thead>
            <tbody>
              <?php
              //Chamando banco de dados


              $sql = "SELECT * FROM cidades ORDER BY id DESC";
              $resultado = mysqli_query($conexao, $sql);
              while ($linha = mysqli_fetch_array($resultado)) {
                echo '<tr>';
                echo  '<td>' . $linha['id'] . '</td>';
                echo  '<td>' . $linha['nome'] . '</td>';
                //Ações                                      
                echo  "<td> <a class='btn btn btn-success'  href='external/altera_session.php?id=$linha[id]&redirect=$redirect' role='button'><i class='bi bi-arrow-bar-right'></i></a>&nbsp;
                                   <a class='btn btn btn-warning'  href='' onclick='alerta($linha[id])' role='button'><i class='bi bi-pencil'></i></a>
                                   </td>";

                echo "</tr>";
              }
              mysqli_close($conexao);
              ?>
            </tbody>
          </table>
        </div><!--Fechando container bootstrap-->
        <?php include("dep_query.php"); ?>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="external/notification.js"></script>
<script>
  function alerta(id) {
    event.preventDefault();
    var resposta = confirm("Deseja realmente editar a cidade? Nem todos os registros serão alterados!");
    if (resposta) {
      window.location.href = "editar_cidade.php?id=" + id;
    }
  }
  let em_cadastro = false;
  if (localStorage.getItem('em_cadastro') == 'true') {
    mostrar_cadastro();
  } else {
    esconder_cadastro();
  }

  function mostrar_cadastro() {
    let cadastro = document.getElementById("cadastro");
    if (cadastro.style.display == "none") {
      cadastro.style.display = "block";
    } else {
      cadastro.style.display = "none";
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