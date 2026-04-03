<?php
include("seguranca.php");
include_once("../classes/categorias_horarios.php");
$ch = new dinamico_horarios();
$horarios = $ch->get_dinamico_horarios_loja($cidade_id);
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>

<body>
  <div class="container-fluid">
  </div>
  <?php
  include("menu.php");
  ?>
  <div class="container">
    <div class="container-principal-produtos">
      <div class="row">
        <div class="col-md-8">
          <h4 class="page-header">Dinâmico por Horários</h4>
        </div>
        <div class="col-md-4">
          <a class="btn btn-success" href="horarios.php" role="button"><i class="bi bi-plus-circle"></i> Novo Dinâmico</a>
        </div>
      </div>
      <hr>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <th>#</th>
            <th>Nome</th>
            <th>Ações</th>
          </thead>
          <tbody>
            <?php
            foreach ($horarios as $linha) {
              echo '<tr>';
              echo  '<td>' . $linha['id'] . '</td>';
              echo  '<td>' . $linha['nome'] . '</td>';
              //Ações  
              echo  '<td>';
              echo "<a class='btn btn-warning' href='editar_din_horarios.php?id=$linha[id]' role='button'><i class='bi bi-pencil'></i></a>&nbsp";
              echo "<a class='btn btn-info' href='../funcoes/duplicar.php?id=$linha[id]&categoria=dinamico_horarios' role='button'><i class='bi bi-arrows-angle-expand'></i></a>&nbsp";
              echo "<a class='btn btn btn-danger' onclick='deletar({$linha['id']})' role='button'><i class='bi bi-trash3'></i></a>&nbsp</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--Fechando container bootstrap-->
  <?php include("dep_query.php"); ?>
  <script>
    function deletar(id) {
      if (confirm("Deseja realmente excluir?")) {
        window.location.href = 'deletar_din_horario.php?id=' + id;
      }
    }
  </script>
</body>

</html>