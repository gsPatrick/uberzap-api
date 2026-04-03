<?php 
header('access-control-allow-origin: *');
include_once "../classes/banners.php";


$b = new Banners();

$cidade_id = $_POST['cidade_id'];

$banners = $b->get_banners($cidade_id);
//prepara os dados para o retorno
$dados_retorno = array();
foreach ($banners as $banner) {
    if ($banner['ativo'] == 1) {
        $dados_retorno[] = array(
            'id' => $banner['id'],
            'img' => $banner['img'],
            'link' => $banner['link']
        );
    }
}

if(count($dados_retorno) > 0) {
    echo json_encode($dados_retorno);
} else {
    echo json_encode(array('error' => 'Nenhum banner ativo encontrado.'));
}
?>