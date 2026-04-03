<?php 
header('access-control-allow-origin: *');
include_once "../classes/cidades.php";
include_once "../classes/franqueados.php";
include "../classes/configuracoes_pagamento.php";


$c = new Cidades();
$f = new Franqueados();
$cp = new configuracoes_pagamento();

$cidade_id = $_POST['cidade_id'];
$dados_franqueado = $f->get_usuarios_cidade($cidade_id);
$dados_franqueado = $dados_franqueado[0];
$dados_cidade = $c->get_dados_cidade($cidade_id);
$token = $cp->read_configuracoes_pagamento($cidade_id);
$dados_retorno = array(
    'telefone' => $dados_franqueado['telefone'],
    'email' => $dados_franqueado['email'],
    'cidade' => $dados_cidade['nome'],
    'latitude' => $dados_cidade['latitude'],
    'longitude' => $dados_cidade['longitude'],
    'token' => $token['token']
);
echo json_encode($dados_retorno);
?>