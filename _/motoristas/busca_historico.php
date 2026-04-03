<?php
include ("../classes/corridas.php");
include ("../classes/seguranca.php");
include ("../classes/tempo.php");
$secret_key= $_POST['secret'];

$s = new seguranca();
if($s->compare_secret($secret_key)){
    $c = new corridas();
    $t = new tempo();
    $id_motorista = $_POST['id_motorista'];
    $data = $_POST['data'];
    $data  = str_replace('/', '-', $data);
    $data = date("Y-m-d", strtotime($data));
    $date_from = date("Y-m-d 00:00:00", strtotime($data));
    $date_to = date("Y-m-d 23:59:59", strtotime($data));
    $corridas = $c->get_corridas_motorista_datas($id_motorista, $date_from, $date_to);

    foreach ($corridas as $key => $value) {
        $corridas[$key]['hora'] = $t -> hora_mysql_para_user($value['date']);
    }
    if($corridas){
        echo json_encode($corridas);
    } else{
        echo "no";
    }

}
?>