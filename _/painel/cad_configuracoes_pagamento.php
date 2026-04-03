<?php 
include("seguranca.php");
include "../classes/configuracoes_pagamento.php";
$c = new configuracoes_pagamento();
$dados = $c->read_configuracoes_pagamento($cidade_id);
$token = $_POST['token'];
if($dados['token'] == ''){
    $c->create_configuracoes_pagamento($cidade_id, $token);
} else {
    $c->update_configuracoes_pagamento($cidade_id, $token);
}
echo "<script>alert('Configurações salvas com sucesso!');window.location.href='configuracoes_pagamento.php';</script>";
?>