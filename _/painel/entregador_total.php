<?
ini_set('default_charset','UTF-8');
include("seguranca.php");
include("style.php");
$app_name= $_SESSION['app_name'];
?>
<!doctype html>
<html lang="pt-br">
    <head><meta charset="utf-8">
      <title> <?php echo $app_name;?> </title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Página Inicial">
		<link href="css/bootstrap-select.min.css" rel="stylesheet">
        <!--LINK CSS-->
        <? echo colors('style.css');?>
        <!--LINK CDN BOOTSTRAP-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      </head>
    <body>
        <div class="container-fluid">
        <?php include("menu.php");
		      include ('conexao.php');
		                session_start();
						$loja= $_SESSION['cateloja'];
		                $cateloja= strtolower($loja);
						$id = $_GET['id_loja'];
						$cpf = $_GET['cpf'];
		                $bd = mysqli_select_db($conexao, "bd_resolv");
		                $sql = "SELECT SUM(taxa) AS 'total' FROM entregadores_historico WHERE cpf = '$cpf' AND loja_id = '$id' AND status = '6'";
                        $resultado = mysqli_query($conexao, $sql);
                        $res = mysqli_fetch_assoc($resultado);
						$tot  = $res['total'];
						$total = str_replace('.',',',$tot);

					    $busca_b = mysqli_query($conexao,"SELECT * FROM entregadores WHERE cpf = '$cpf'");
						$dados = mysqli_fetch_assoc($busca_b);
					?>
        </div>
        <div class="container">
        <div class="container-principal-produtos">
         <h4 class="page-header">Total: </h4>
         <hr>
            
              <div class="row">
                <div class="form-group col-md-4">
                  <label>Nome do Motorista:</label>
                  </b><?php echo $dados['nome'];?><br>
				  </div>
				  <div class="form-group col-md-4">
				  <label>Telefone:</label>
                 <?php echo $dados['telefone'];?><br>
				 </div>
				 <div class="form-group col-md-4">
				 <label>Total a pagar: R$</label>
				 <?php echo $total;?><br>
				 </div>
											   
          </div>
		  <br>
       </div><!--Fechando container bootstrap-->
  <?php include("dep_query.php");?>     
  </body>
</html>