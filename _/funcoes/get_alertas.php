<?php 
include "../classes/corridas.php";
$c = new corridas();
if(isset($_POST['cidade_id'])){
    $cidade_id = $_POST['cidade_id'];
    $last_id = $_POST['last_id'];
    //pega ultimo id inserido na tabela corridas
    $last_id_corrida = $c->get_last_id_corrida($cidade_id);
    if($last_id_corrida > $last_id){
        echo "1";
    }else{
        echo "0";
    }
}
?>