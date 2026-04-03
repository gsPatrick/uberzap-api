<?php
include ("../classes/mensagens.php");
include ("../classes/seguranca.php");
include ("../classes/tempo.php");
$secret_key= $_POST['secret'];

$s = new seguranca();
if($s->compare_secret($secret_key)){
    $m = new mensagens();
    $t = new tempo();
    $id_corrida = $_POST['id_corrida'];
    $mensagens = $m ->get_all_msg($id_corrida);
    if($mensagens){
        foreach ($mensagens as $key => $value) {
            $mensagens[$key]['hora'] = $t->data_mysql_para_user($value['date']) . " às " . $t->hora_mysql_para_user($value['date']);
        }
        echo json_encode($mensagens);
    }else{
        echo "no";
    }
}

?>