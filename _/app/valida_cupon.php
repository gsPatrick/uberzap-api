<?php
header("Access-Control-Allow-Origin: *");
include '../classes/cupons.php';
include '../classes/tempo.php';
include_once "../classes/clientes.php";
$t = new Tempo();
$cupons = new cupons();
$c = new Clientes();

$senha = $_POST['senha'];
$telefone = $_POST['telefone'];

$cliente = $c->login($telefone, $senha);

if ($cliente) {
    $user_id = $cliente['id'];
    $cidade_id = $cliente['cidade_id'];
    if (isset($_POST['cupom']) && !empty($_POST['cupom'])) {
        $valor  = $_POST['valor'];
        $valor = str_replace(',', '.', $valor);
        $cupom = addslashes($_POST['cupom']);
        $dados_cupon = $cupons->get_cupon_nome($cupom, $cidade_id);
        $usado = $cupons->verifica_cupon_used($cidade_id, $cupom, $user_id);
        $t->data_fim = $dados_cupon['validade'];
        $desconto = 0;
        $valor_min = $dados_cupon['valor_min'];
        $valor_min = str_replace(',', '.', $valor_min);
        if ($t->tempo_passou() > 0) {
            $status =  "Cupom expirado";
        } else {
            if ($dados_cupon['quantidade'] <= 0) {
                $status =  "Cupom esgotado";
            } else {
                if ($usado && $dados_cupon['uso_unico'] == 1) {
                    $status =  "Cupom já utilizado";
                } else {
                    if ($valor_min > $valor) {
                        $status =  "Valor mínimo não atingido";
                    } else {
                        if ($dados_cupon['primeira_compra'] == 1 && $cupons->verifica_uso_loja($cidade_id, $user_id)) {
                            $status =  "Cupon para Primeira Compra";
                        } else {
                            if ($dados_cupon['tipo_desconto'] == 2) {
                                $desconto = $valor - $dados_cupon['valor'];
                                $desconto = number_format($desconto, 2, ',', '.');
                            } else {
                                $desconto = $valor - ($valor * ($dados_cupon['valor'] / 100));
                                $desconto =  number_format($desconto, 2, ',', '.');
                            }
                            $status = "Cupom aplicado!";
                            // $cupons->add_cupon_used($_POST['loja_id'], $cupom, $_POST['user_id']);
                            // $cupons->diminui_quantidade($dados_cupon['id']);

                        }
                    }
                }
            }
        }
        echo json_encode(array('status' => $status, 'desconto' => $desconto));
    }else{
        echo json_encode(array('status' => 'Dados do cupom inválidos'));
    }
} else {
    echo json_encode(array('status' => 'Usuário não encontrado'));
}
