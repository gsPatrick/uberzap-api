<?PHP
include("seguranca.php");
$mensagem = $_POST['mensagem'];
$titulo = $_POST['titulo'];


function sendMessage($player_id, $msg, $title){
    include("../app/conexao.php");
    $content = array(
        "en" => $msg
        );
    $headings = array(
        "en" => $title
    );

    $fields = array(
        'app_id' => $app_id,
        'included_segments' => array('All'),
        'data' => array("foo" => "bar"),
        'large_icon' =>"ic_launcher_round.png",
        'contents' => $content,
        "headings" => $headings
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic '.$key_one_signal.''));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

$player = "All";
$response = sendMessage($player, $mensagem, $titulo);
header("Location:enviar_push.php?ok=sucess");	

?>

