<?php
include("seguranca.php");
include("../classes/franqueados.php");
include("../classes/motoristas.php");
$f = new franqueados();
$m = new motoristas();
$lista_motoristas = $m->get_motoristas($cidade_id, false);
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
          <h4 class="page-header">Lista de Motoristas</h4>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <th>ID</th>
              <th>Nome</th>
              <th>email</th>
              <th>Telefone</th>
              <th>Saldo</th>
              <th>Ações</th>
              <th>Status</th>   
            </thead>
            <tbody>
              <?php
              foreach ($lista_motoristas as $linha) {
                echo '<tr>';
                echo  '<td>' . $linha['id'] . '</td>';
                echo  '<td>' . $linha['amway'] . '</td>';
              echo  '<td>' . $linha['nome'] . '</td>';
              echo  '<td>' . $linha['email'] . '</td>';
              echo  '<td>' . $linha['telefone'] . '</td>';
              //Ações                                      
              echo  "<td><button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
              echo "<a class='btn btn-warning' href='editar_motorista.php?id=$linha[id]' role='button'><i class='bi bi-pencil'></i></a>&nbsp";
              echo "<a class='btn btn-info' href='../funcoes/duplicar.php?id=$linha[id]&categoria=entregadores' role='button'><i class='bi bi-arrows-angle-expand'></i></a>&nbsp";
              echo "<a class='btn btn btn-danger' href='#' onclick= 'return confirmar($linha[id]);' role='button'><i class='bi bi-trash'></i></a>&nbsp";
              echo "<a class='btn btn-outline-success' href='desativar_motorista.php?id=$linha[id]&status=1' role='button'><i class='bi bi-check2-circle'></i></a>&nbsp</td>";
              if($linha['online'] == '1'){
                echo "<td><a class='btn btn-outline-success' href='#' role='button'>Online</a>&nbsp</td>";
              }else{
                echo "<td><a class='btn btn-outline-danger' href='#' role='button'>Offline</a>&nbsp</td>";
              }                                   
                

                echo "</tr>";
              ?>
                <!--Inicio Modal.-->
              <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <center>
                        <h3 class="modal-title" id="myModalLabel"> Motorista: <?php echo $linha['nome']; ?></h3>
                      </center>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                      <b>amway: </b><?php echo $linha['amway']; ?><br>
                      <b>Nome: </b><?php echo $linha['nome']; ?><br>
                      <b>email: </b><?php echo $linha['email']; ?><br>
                      <b>CPF: </b><?php echo $linha['cpf']; ?><br>
                      <b>Telefone: </b><?php echo $linha['telefone']; ?><br>
                      <b>Senha: </b><?php echo $linha['senha']; ?><br>
                      <b>Veículo: </b><?php echo $linha['veiculo']; ?><br>
                      <b>Placa: </b><?php echo $linha['placa']; ?><br>
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
    </div><!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
</body>

</html>

<script>
function confirmar($id){
  Swal.fire({
    title: 'Deseja realmente excluir?',
    text: "Você não poderá reverter esta ação!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: "Cancelar",
    confirmButtonText: 'Sim, excluir!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "deletar_motorista.php?id=" + $id;
    }
  })
}
</script>