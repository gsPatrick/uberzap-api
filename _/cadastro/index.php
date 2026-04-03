<?php
include_once "../classes/cidades.php";
$c = new cidades();
?>

<!doctype html>
<style>
  .container-bg {
    background: black;
  }
</style>
<?php include "head.php"; ?>

<body>
  </div>
  <div class="container">
    <div class="container-principal-produtos">
      <h4 class="page-header">CADASTRO (serão aceitos imagens em JPG ou JPEG) </h4>
      <hr>
      <form action="cad_info.php" method="POST" enctype="multipart/form-data" name="upload">
        <div class="row">
          <div class="form-group col-md-8">
            <label>Estado:</label>
            <select name="cidade_id" class="form-control form-control-sm col-md-10 col-sm-10"
              placeholder="Selecione a Estado" required>
              <?php
$dados = $c->get_cidades();
foreach ($dados as $dado) {
  echo "<option value='" . $dado['id'] . "'>" . $dado['nome'] . "</option>";
}

?>
            </select>
            <br>
            <label>Nome:</label>
            <input class="form-control form-control-sm col-md-08 col-sm-09" type="text" name="nome" required
              placeholder="ex: Ronaldo" />
            </br>
            <label>email:</label>
            <input class="form-control form-control-sm col-md-08 col-sm-09" type="text" name="email" required
              placeholder="email" />
            </br>
            <label>CPF:</label>
            <input class="form-control form-control-sm col-md-08 col-sm-09" type="text" name="cpf" required
              placeholder="ex: 02154546484" />
            </br>
            <label>Senha para acesso:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="senha" required
              placeholder="Senha" />
            </br>
            <label>Telefone:</label>
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="telefone" required
              placeholder="ex: 54999876542" />
            </br>
            <label>Modelo do veículo:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="veiculo"
              placeholder="Ex. Gol G5" />
            <br>
            <label>Placa do veículo:</label>&nbsp
            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="placa"
              placeholder="Ex. SPO 7896" />
            <br>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Foto CNH frente e verso</label>
                <input class="form-control" type="file" required name="img_cnh">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Foto Documento do Veículo</label>
                <input class="form-control" type="file" required name="img_documento">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Foto Frente do Veículo</label>
                <input class="form-control" type="file" required name="img_frente">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Foto Lateral do Veículo</label>
                <input class="form-control" type="file" required name="img_lateral">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Foto sua (selfie)</label>
                <input class="form-control" type="file" required name="img_selfie">
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-8">
                <!--Realizando Upload de Imagem-->
                <label class="control-label">Antecedentes Criminais</label>
                <input class="form-control" type="file" required name="img_antecedentes">
              </div>
            </div>
          </div>
        </div>

        <div style="display: flex;width: 100%;justify-content: center;">
          <button id="cadastro" type="submit" class="btn btn-primary" name="btn_enviar">Cadastrar Informações</button>
        </div>
      </form>
    </div>
    <br>
  </div><!--Fechando container bootstrap-->
  <?php
include "dep_query.php"; ?>
  <script>
    $(document).ready(function () {
      $("#cadastro").click(function (event) {
        // Verifica se os campos obrigatórios estão preenchidos
        if (validarCampos()) {
          var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> fazendo upload...';
          // Desabilita o botão e adiciona um spinner
          $(this).html(loadingText);
          $(this).prop("disabled", true);
          // Continua com o envio do formulário
          document.upload.submit();
        } else {
          // Se os campos obrigatórios não estiverem preenchidos, evita o envio do formulário
          event.preventDefault();
          alert("Preencha todos os campos obrigatórios.");
        }
      });

      // Função para validar os campos obrigatórios
      function validarCampos() {
        var camposValidos = true;
        $("form[name='upload'] :input[required]").each(function () {
          if (!$(this).val()) {
            camposValidos = false;
            return false; // Sai do loop se um campo não estiver preenchido
          }
        });
        return camposValidos;
      }
    });
  </script>
</body>

</html>