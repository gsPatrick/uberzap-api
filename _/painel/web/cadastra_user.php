<?
//include("seguranca.php");
include("../../admin/style.php");
include("../../app/conexao.php");
?>
<!doctype html>
<style>
.container-bg {
    background: black;
}
</style>
<html lang="pt-br">
    <head><meta charset="utf-8">
      <title> <?php echo $nome_app;?> </title>
      <script>
        function alerta(msg) {
        alert(msg);
        }
        </script>
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Página Inicial">
        <!--LINK CSS-->
         <? echo colors('style.css');?>
        <!--LINK CDN BOOTSTRAP-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      </head>
    <body>
    
       <!-- <div class="container-bg">
             <div style="display: flex;width: 100%;justify-content: center;">
               <img src="../../assets/img/logo.png" class="img-fluid" width="300px;" alt="Responsive image">
             </div> -->
       </div> 
        <div class="container">
        <div class="container-principal-produtos">
          
         <h4 class="page-header">CADASTRO </h4>
         <hr>
        
            <form action="cad_info.php" method="POST" enctype="multipart/form-data" name="upload">
    
              <div class="row">
                   <div class="form-group col-md-8">
                  <label>Cidade:</label>

                    <select  name="cidade" class="form-control form-control-sm col-md-10 col-sm-10" placeholder="Selecione a Cidade" required>

					<?php

		                $sql = "SELECT id, cidade FROM users ORDER BY cidade ASC";

                        $resultado = mysqli_query($conexao, $sql);

						while($lista = mysqli_fetch_assoc($resultado)){ ?>

							<option value="<?php echo $lista['id']; ?>"><?php echo $lista['cidade']; ?></option> <?php

						}

					?>

                   </select>

                <br>
				<label>Nome:</label>
				  <input class="form-control form-control-sm col-md-08 col-sm-09"  type="text" name="nome" required placeholder="ex: Ronaldo" />
				  
				  </br>
				  <label>CPF:</label>
				  <input class="form-control form-control-sm col-md-08 col-sm-09"  type="text" name="cpf" required placeholder="ex: 02154546484" />
				  </br>
				  <label>Senha para acesso:</label>
				  <input class="form-control form-control-sm col-md-09 col-sm-09"  type="text" name="senha" required placeholder="Senha" />
				  </br>
				  <label>Telefone:</label>
				  <input class="form-control form-control-sm col-md-09 col-sm-09"  type="text" name="telefone" required placeholder="ex: 54999876542" />
				  </br>
				  <label>Modelo do veículo:</label>&nbsp 
                  <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="veiculo" placeholder="Ex. Gol G5" />
				  <br>
				  <label>Placa do veículo:</label>&nbsp 
                  <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" required name="placa" placeholder="Ex. SPO 7896" />
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
                </div>
              </div>
			  
			 <div style="display: flex;width: 100%;justify-content: center;">
              <button id="cadastro" type="submit" class="btn btn-primary" name="btn_enviar" >Cadastrar Informações</button>
              </div>
            </form>
          </div>
          <br>
       </div><!--Fechando container bootstrap-->
  <?php 
  
  
  if(!empty($_GET['ok'])){ ?>
  <script>alerta("Cadastrado com Sucesso");</script>
       <? }   
        if(!empty($_GET['erro'])){ ?>
    <script>alerta("Usuário ja cadastrado!");</script>
       <? } include "dep_query.php";?> 
    <script>
        $(document).ready(function() {
            $("#cadastro").click(function() {
                var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> fazendo upload...';
                // disable button
                // add spinner to button
                 $(this).html(
                  loadingText
                  );
                 $(this).prop("disabled", true);
                
                 document.upload.submit();
            });
      });
    </script>
  </body>
</html>