<?PHP


$secret_key= $_POST['secret'];
$id_pai= $_POST['id'];
$id_signal = $_POST['id_signal'];
$mensagem = $_POST['mensagem'];


function sendMessage($player_id, $msg){
    include ("conexao.php");
    $content = array(
        "en" => $msg
        );

    $fields = array(
        'app_id' => $app_id,
        'include_player_ids' => array("$player_id"),
        'data' => array("foo" => "bar"),
        'large_icon' =>"ic_launcher_round.png",
        'contents' => $content
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic MzhmNmJkNzQtN2I0YS00Y2MzLWFhZDYtYjA5Zjk0OGZlYjY4'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

if($secret_key==$secret){


$response = sendMessage($id_signal, $mensagem);
$return["allresponses"] = $response;
echo json_encode($return);


}else{
	echo "no";
}

?>

