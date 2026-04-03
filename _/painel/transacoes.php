<?php
include("seguranca.php");
include_once "../classes/transacoes.php";
include_once "../classes/clientes.php";
include_once "../classes/tempo.php";
$t = new transacoes($cidade_id);
$c = new clientes();
$tempo = new tempo();

// Verifica se a configuração de itens por página foi selecionada pelo usuário
if (isset($_GET['itens_por_pagina'])) {
    $itensPorPagina = $_GET['itens_por_pagina'];
    // Salva a configuração selecionada no armazenamento local
    if ($itensPorPagina > 0) {
        $_SESSION['itens_por_pagina'] = $itensPorPagina;
    }
} else {
    // Verifica se há uma configuração salva no armazenamento local
    if (isset($_SESSION['itens_por_pagina'])) {
        $itensPorPagina = $_SESSION['itens_por_pagina'];
    } else {
        // Caso contrário, utiliza um valor padrão
        $itensPorPagina = 50;
    }
}

?>

<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
        <div class="container">
            <div class="container-principal-produtos">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="page-header">Histórico de Transações</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group col-md-12">
                            <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" id="pesquisa" name="pesquisa" placeholder="Pesquisar" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="itensPorPagina">Itens por página:</label>
                        <select id="itensPorPagina" name="itensPorPagina" class="form-control form-control-sm col-md-2">
                            <option value="10" <?php if ($itensPorPagina == 10) echo "selected"; ?>>10</option>
                            <option value="25" <?php if ($itensPorPagina == 25) echo "selected"; ?>>25</option>
                            <option value="50" <?php if ($itensPorPagina == 50) echo "selected"; ?>>50</option>
                            <option value="100" <?php if ($itensPorPagina == 100) echo "selected"; ?>>100</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <!--Controlador de tamanho e margem da tabela-->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Metodo</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <!-- <th>Ações</th> -->
                    </thead>
                    <tbody>
                        <?php
                        // Configurações de paginação
                        if ($cidade_id  != 0) {
                            $totalItens = count($t->getByCidadeId($cidade_id));
                            $totalPaginas = ceil($totalItens / $itensPorPagina);

                            // Verifica a página atual
                            $paginaAtual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
                            $indiceInicial = ($paginaAtual - 1) * $itensPorPagina;
                            $indiceFinal = $indiceInicial + $itensPorPagina;

                            // Obtém os transacoes da página atual
                            $transacoes = array_slice($t->getByCidadeId($cidade_id), $indiceInicial, $itensPorPagina);
                        } else {
                            $totalPaginas = 0;
                            $transacoes = array();
                        }
                        //inverte a ordem do array
                        $transacoes = array_reverse($transacoes);
                        foreach ($transacoes as $linha) {
                            echo '<tr>';
                            echo  '<td>' . $linha['id'] . '</td>';
                            echo  '<td>' . $tempo->data_mysql_para_user($linha['date']) . " - " . $tempo->hora_mysql_para_user($linha['date']) . '</td>';
                            echo  '<td>' . $c->get_cliente_id($linha['user_id'])['nome'] . '</td>';

                            if (strpos($linha['metodo'], 'CREDITO') !== false) {
                                echo  '<td><span class="badge badge-success">' . $linha['metodo'] . '</span></td>';
                            } else if ($linha['metodo'] == "RECARGA ONLINE") {
                                echo  '<td><span class="badge badge-primary">' . $linha['metodo'] . '</span></td>';
                            } else {
                                echo  '<td><span class="badge badge-danger">' . $linha['metodo'] . '</span></td>';
                            }
                            if ($linha['status'] == "CONCLUIDO") {
                                echo  '<td><span class="badge badge-success">' . $linha['status'] . '</span></td>';
                            } else if ($linha['status'] == "PENDENTE") {
                                echo  '<td><span class="badge badge-warning">' . $linha['status'] . '</span></td>';
                            } else {
                                echo  '<td><span class="badge badge-danger">' . $linha['status'] . '</span></td>';
                            }
                            if (strpos($linha['metodo'], 'DEBITO') !== false) {
                            echo  '<td> -' . $linha['valor'] . '</td>';
                            } else {
                                echo  '<td> +' . $linha['valor'] . '</td>';
                            }
                            // Ações
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Paginação -->
            </div>
        </div>
    </div>
    
    <div id="rodape" class="rodape">
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <?php
                for ($pagina = 1; $pagina <= $totalPaginas; $pagina++) {
                    echo '<li class="page-item ' . ($pagina == $paginaAtual ? 'active' : '') . '"><a class="page-link" href="?pagina=' . $pagina . '&itens_por_pagina=' . $itensPorPagina . '">' . $pagina . '</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
    <!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <script>
        function deletar(id) {
            if (confirm("Deseja realmente deletar este produto?")) {
                window.location.href = 'deletar.php?id=' + id + '&location=listar_transacoes.php&categoria=transacoes'
            }
        }
        $('#pesquisa').keyup(function() {
            var nomeFiltro = $(this).val().toLowerCase();
            $('table tbody').find('tr').each(function() {
                let conteudoCelula = $(this).find('td:nth-child(2)').text();
                let conteudoCelula2 = $(this).find('td:nth-child(3)').text();
                var corresponde = conteudoCelula.toLowerCase().indexOf(nomeFiltro) >= 0;
                var corresponde2 = conteudoCelula2.toLowerCase().indexOf(nomeFiltro) >= 0;
                $(this).css('display', corresponde || corresponde2 ? '' : 'none');
            });
        });
        $('#cadastrar').on('click', function() {
            window.location.href = 'cadastrar_transacoes.php';
        });

        // Atualiza a página quando o usuário selecionar a quantidade de itens por página
        $('#itensPorPagina').change(function() {
            var itensPorPagina = $(this).val();
            window.location.href = '?pagina=1&itens_por_pagina=' + itensPorPagina;
        });

        if (localStorage.getItem('msg') != null) {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: localStorage.getItem('msg'),
                showConfirmButton: false,
                timer: 1000
            })
            localStorage.removeItem('msg');
        }
    </script>
</body>

</html>