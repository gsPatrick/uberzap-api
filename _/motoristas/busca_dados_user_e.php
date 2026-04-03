<?php
include ("conexao.php");

$secret_key= $_POST['secret'];
$id_pai = $_POST['id'];


if($secret_key==$secret){
					
		$busca = "SELECT id, cidade, add_taxa, estado, telefone FROM users WHERE id = '$id_pai'";
		$resultado = mysqli_query($conexao, $busca);
		$cont = mysqli_num_rows($resultado);
				if($cont>0){
						   while ($array = mysqli_fetch_assoc($resultado)) {
							$dados[] = $array;
						   }
						   echo json_encode($dados);
						   }else{
							   echo "no";
						   }

}else{
	echo "no";
}
	

?>