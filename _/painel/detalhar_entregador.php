<?php
header("Content-type: text/html; charset=utf-8");
include("seguranca.php");
include("style.php");
include("../app/conexao.php");
?>
<!doctype html>
<html lang="pt-br">
    <head><meta charset="utf-8">
      <title> <?php echo $app_name;?> </title>
        <script>
			function alerta(id)
			{
			var resposta = confirm("Deseja realmente marcar todos como pago?");
    			if(resposta){
    			  window.location.href =  'pagar.php?cpf='+id;
    		 	}
			}
			</script>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Lista de Motoristas">
        <!--LINK CSS-->
         <? echo colors('style.css');?>
        <!--LINK CDN BOOTSTRAP-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
      </head>
    <body>
        <div class="container-fluid">
		</div>
		<?php
		include("menu.php");
	
		$date_from = $_POST['date_from'];
    	 $date_to =  $_POST['date_to'];
    	 if(empty($date_from)){
    	
    	 $date_from = date('Y-m')."-01";
    	 $date_to =  date('Y-m')."-30";
	     }
	    $id= $_SESSION['id'];
		$cpfEntregador = $_GET['cpf'];
		$nomeEntregador = $_GET['nome'];
        $taxa=0;
		$vlue=0;
		$taxa_app =0;
		$entregas_recusadas=0;
		
		
		
		 $sql = "SELECT * FROM entregadores_historico_$id WHERE cpf= '$cpfEntregador'
		 AND date between '$date_from' and '$date_to' 
		 AND status = '4' ORDER BY id DESC";
		 $resultado = mysqli_query($conexao, $sql);
		 $totalEntregas = mysqli_num_rows($resultado);
		 
		 
		 $sql2 = "SELECT * FROM entregadores_$id WHERE cpf = '$cpfEntregador'";
		 $result = mysqli_query($conexao, $sql2);
		 $result = mysqli_fetch_assoc($result);
		 $porcentagem = $result['taxa'];
		 $nomeEntregador = $result['nome'];
		 
		 
		 
		 while($dados = mysqli_fetch_array($resultado)){
		  if($dados['pago']=='1'){
			 $taxa= $taxa+$dados['taxa'];
		  }else{
		      $pago= $pago+$dados['taxa'];
		  }
		}
		$valor_acertar = $taxa*$porcentagem/100;
		$valor_total = $taxa+$pago;
		 
		?>
		
        <div class="container">
          <div class="container-principal-produtos">
             <div class="row">
          <h4 class="page-header">Pesquisar entre:</h4>
		  <div class="form-group col-md-2">
		   <form action="detalhar_entregador.php?cpf=<?echo $cpfEntregador;?>" method="POST" enctype="multipart/form-data" name="upload">
			<input type="date" id="date_from" value="<? echo $date_from;?>" name="date_from">
			</div>
			 <div class="form-group col-md-1">
			 <h4 class="page-header"> Até </h4>
			 </div>
			 <div class="form-group col-md-2">
			 <input type="date" id="date_to" value="<? echo $date_to;?>" name="date_to">
			 </div>
			 <div class="form-group col-md-2">
		    <input type="submit" class="btn btn-primary" name="btn_enviar" value="Pesquisar">
			</div>
        	</div>
		  <h4 class="page-header">Motorista <?php echo $nomeEntregador;?></h4><hr>
		  <label>Total em entregas R$: <?php echo str_replace('.',',',round($valor_total,2));?></label><hr>
		  <div class="row">
		  <div class="form-group col-md-3">
		  <label>Saldo devedor : </label>
		  </label><font color="red"><?php echo "R$ ".str_replace('.',',',number_format((float)$valor_acertar, 2, '.', ''));?></font> </label>
		  </div>
		  <div class="form-group col-md-3">
		  <a class='btn btn-outline-danger' href="#" 
		  onclick='alerta(<? echo $cpfEntregador;?>)' 
		  role='button'>Marcar como pago</a>
		  </div>
		 </div>
		 
		 
		  <div class="row">
			  <div class="form-group col-md-3">
			  <label>Total de entregas: </label>
			  <label><font color="green"><?php echo $totalEntregas;?></font></label><hr>
			  </div>
			  <div class="form-group col-md-3">
			  <label>Porcentagem cobrada: </label>
			  <label><font color="green"><?php echo $porcentagem." %";?></font></label><hr>
			  </div>
		  </div>
		  
		  <h4 class="page-header">Lista de entregas feitas:</h4>
		  <hr>
               <div class="table-responsive">
                    <table class="table table-hover">
                            <thead>
                                
                                <th>Cliente: </th>
								<th>Data</th>
								<th>Hora</th>
								<th>Taxa</th>
								<th>Valor R$ </th>
                                <th>Ações</th>
                            </thead>
                          <tbody>                      
                            <?php
							$resultado2 = mysqli_query($conexao, $sql);
                            while($linha = mysqli_fetch_array($resultado2))
                            {
								
								$taxa_app_unic = $linha['taxa']/100*$porcentagem;
								$taxa_app_unic = number_format((float)$taxa_app_unic, 2, '.', '');
								$taxa_app_unic = str_replace('.',',',$taxa_app_unic);
                               echo '<tr>';                  
                                  
                                   echo  '<td>'.$linha['nome_cliente'].'</td>'; 
								   echo  '<td>'.$linha['data'].'</td>';
								   echo  '<td>'.$linha['hora'].'</td>';
								   if($linha['pago']==1&&$linha['status']!=5){
								   echo  '<td><font color="red">'.$taxa_app_unic.'</font></td>';
								   }else if ($linha['pago']!=1&&$linha['status']!=5){
									 echo  '<td><font color="green">'.$taxa_app_unic.'</font></td>';
								   } else if ($linha['status']==5){								 
								   echo  '<td><label><font color="red">Recusada</font></label></td>';
								   }								 
								   echo  '<td>'.str_replace('.',',',$linha['taxa']).'</td>';
                                   //Ações                                      
                                   echo  "<td><button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'>Dados</button></td>"; 
                                                                   
                              echo "</tr>";
                              ?>
                                 <!--Inicio Modal.-->
                                  <div class="modal fade" id="myModal<?php echo $linha['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                           <center><h4 class="modal-title" id="myModalLabel"> Motorista: <?php echo $nomeEntregador;?></h4></center>
                                          </div>
                                          <div class="modal-body">
                                               <b>Cliente: </b><?php echo $linha['nome_cliente'];?><br>
										       <b>Endereço inicial: </b><?php echo $linha['endereco'];?><br>
                                               <b>Endereço final: </b><?php echo $linha['endereco_fim'];?><br><hr>
                                               <b>Forma de Pagamento: </b><?php echo $linha['pagamento'];?><br>
                                               <b>Tempo estimado: </b><?php echo $linha['tempo'];?><br>
                                               <b>EnDistância estimada: </b><?php echo $linha['km']."km";?><br>
                                               

                                              

                                          <!--<b>Data:</b><?php //echo $linha['dt_insercao'];?><br><br>-->             
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
                              mysqli_close($conexao);
                              ?>
                            </tbody>
                      </table>
            </div>
          </div>
       </div><!--Fechando container bootstrap-->
  <?php include("dep_query.php");?>     
  </body>
</html>