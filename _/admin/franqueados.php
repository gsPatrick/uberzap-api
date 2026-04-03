<?php
include("seguranca.php");
include_once $_SERVER['DOCUMENT_ROOT'] . "/_/bd/conexao.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/_/classes/franqueados.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/_/classes/cidades.php";
$c = new cidades();
$franqueados = new franqueados();
$usuarios = $franqueados->get_usuarios_cidade($_SESSION['cidade_id']);
$cidades = $c->get_cidades();
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>
<?php include_once "external/verifica_login.php"; ?>

<body>
    <div class="container-fluid">
    </div>
    <div class="container">
        <div class="container-principal-produtos">
            <div id="cadastro" style="display: none;">
                <h4 class="page-header">Cadastro de Usuários</h4>
                <hr>
                <form action="cad_franqueados.php?cidade_id=<?php echo $cidade_id; ?>" method="POST" enctype="multipart/form-data" name="upload">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Nome do Usuário:</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="nome" placeholder="Ex: João Alvez" required />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Login:</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="usuario" placeholder="Ex: joaoalves" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Senha para acesso:</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="senha" placeholder="Senha" required />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Cidade:</label>
                            <select class="form-control form-control-sm col-md-10 col-sm-10" name="loja_id">
                                <option value="0">Selecione a Cidade</option>
                                <?php
                                foreach ($cidades as $cidade) {
                                    echo '<option value="' . $cidade['id'] . '">' . $cidade['nome'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Telefone Contato (whats):</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="telefone" placeholder="Exemplo 48991174663" required />
                        </div>
                        <div class="form-group col-md-4">
                            <label>E-mail:</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="text" name="email" placeholder="E-mail" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="porcentagem">Porcentagem a ser cobrada</label>
                            <input class="form-control form-control-sm col-md-10 col-sm-10" type="number" name="comissao" id="porcentagem" placeholder="Ex: 10" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar">
                        </div>
                    </div>
                </form>
                <hr>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h4 class="page-header">Lista de Usuários</h4>
                </div>
                <div class="col-md-3">
                    <div class="form-group col-md-12">
                        <input class="form-control form-control-sm col-md-12 col-sm-12" type="text" id="pesquisa" name="pesquisa" placeholder="Pesquisar" />
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary btn-sm" style="float: right;" onclick="mostrar_cadastro()">Cadastrar</button>
                </div>
            </div>
            <hr>
            <!--Controlador de tamanho e margem da tabela-->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <th>Nome</th>
                        <th>Cidade</th>
                        <th>Ações</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($usuarios as $linha) {
                            echo '<tr>';
                            echo  '<td>' . $linha['nome'] . '</td>';
                            echo  '<td>' . $c->get_cidade($linha['cidade_id']) . '</td>';
                            //Ações                                      
                            echo  "<td><button type='button' class='btn btn-info'  data-toggle='modal' data-target='#myModal$linha[id]'><i class='bi bi-eye'></i></button>&nbsp";
                            echo "<button type='button' class='btn btn-warning' data-toggle='modal' data-target='#modal_editar_$linha[id]'><i class='bi bi-pencil'></i></button>&nbsp";
                            echo "<a class='btn btn btn-danger' href='#' onclick='deletar({$linha['id']})' role='button'><i class='bi bi-trash3'></i></a>&nbsp</td>";

                            echo "</tr>";
                        ?>
                            <!--Inicio Modal.-->
                            <div class="modal fade" id="myModal<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <center>
                                                <h3 class="modal-title" id="myModalLabel"> Usuário id: <?php echo $linha['id']; ?></h3>
                                            </center>
                                        </div>
                                        <div class="modal-body">
                                            <br><br>
                                            <b>Nome: </b><?php echo $linha['nome']; ?><br>
                                            <b>Usuario: </b><?php echo $linha['usuario']; ?><br>
                                            <b>Senha: </b><?php echo $linha['senha']; ?><br>
                                            <b>Telefone: </b><?php echo $linha['telefone']; ?><br>
                                            <b>E-mail: </b><?php echo $linha['email']; ?><br>
                                            <b>Cidade: </b><?php echo $c->get_cidade($linha['cidade_id']); ?><br>
                                            <b>Comissão: </b><?php echo $linha['comissao']; ?> %<br>
                                            <hr>
                                            <!--<b>Data:</b><?php //echo $linha['dt_insercao'];
                                                            ?><br><br>-->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--fim modal-->
                            <!--Inicio Modal Editar.-->
                            <!-- Inicio Modal Editar -->
                            <div class="modal fade" id="modal_editar_<?php echo $linha['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal_editar_<?php echo $linha['id']; ?>_label">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modal_editar_<?php echo $linha['id']; ?>_label">Editar Usuário</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Formulário de edição -->
                                            <form action="editar_franqueado.php" method="POST">
                                                <input type="hidden" name="id" value="<?php echo $linha['id']; ?>">
                                                <div class="form-group">
                                                    <label for="nome">Nome:</label>
                                                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $linha['nome']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="usuario">Usuário:</label>
                                                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $linha['usuario']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="senha">Senha:</label>
                                                    <input type="text" class="form-control" id="senha" name="senha" value="<?php echo $linha['senha']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="telefone">Telefone:</label>
                                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo $linha['telefone']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">E-mail:</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $linha['email']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="cidade">Cidade:</label>
                                                    <select class="form-control" id="cidade" name="cidade_id">
                                                        <?php
                                                        $cidades = $c->get_cidades();
                                                        foreach ($cidades as $cidade) {
                                                            $selected = ($cidade['id'] == $linha['cidade_id']) ? 'selected' : '';
                                                            echo '<option value="' . $cidade['id'] . '" ' . $selected . '>' . $cidade['nome'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comissao">Comissão:</label>
                                                    <input type="number" class="form-control" id="comissao" name="comissao" value="<?php echo $linha['comissao']; ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                            </form>
                                            <!-- Fim do formulário de edição -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim Modal Editar -->
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!--Fechando container bootstrap-->
            <?php include("dep_query.php"); ?>
            <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
            <script>
                function deletar(id) {
                    Swal.fire({
                        title: 'Você tem certeza?',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: 'Sim',
                        denyButtonText: `Não`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            window.location.href = "deletar_franqueado.php?id=" + id;
                        } else if (result.isDenied) {
                            Swal.fire('Usuário não deletado', '', 'info')
                        }
                    })

                }

                let em_cadastro = false;
                if (localStorage.getItem('em_cadastro') == 'true') {
                    mostrar_cadastro();
                }

                function mostrar_cadastro() {
                    let cadastro = document.getElementById("cadastro");
                    if (cadastro.style.display == "none") {
                        cadastro.style.display = "block";
                    } else {
                        cadastro.style.display = "none";
                    }
                }

                function mostrar_cadastro() {

                    let cadastro = document.getElementById("cadastro");
                    if (cadastro.style.display == "none") {
                        localStorage.setItem('em_cadastro', true);
                        $("#cadastro").animate({
                            opacity: 1,
                            left: "+=50",
                            height: "toggle"
                        }, 400, function() {
                            // Animation complete.
                        });
                    } else {
                        localStorage.setItem('em_cadastro', false);
                        $("#cadastro").animate({
                            opacity: 1,
                            left: "+=50",
                            height: "toggle"
                        }, 400, function() {
                            // Animation complete.
                        });
                    }
                }
                $('#pesquisa').keyup(function() {
                    var nomeFiltro = $(this).val().toLowerCase();
                    $('table tbody').find('tr').each(function() {
                        var conteudoCelula = $(this).find('td:first').text();
                        var corresponde = conteudoCelula.toLowerCase().indexOf(nomeFiltro) >= 0;
                        $(this).css('display', corresponde ? '' : 'none');
                    });
                });
            </script>
</body>

</html>