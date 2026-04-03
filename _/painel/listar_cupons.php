<?php
include("seguranca.php");
include_once "../classes/cupons.php";
include_once "../classes/tempo.php";
$c = new cupons();
$t = new tempo();
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
        <div class="container">
            <div class="container-principal-produtos">
                <div id="cadastro" style="display: none;">
                    <h4 class="page-header">CADASTRO DE CUPONS</h4>
                    <hr>
                    <form action="cad_cupon.php" method="POST" enctype="multipart/form-data" name="upload">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Código do cupom:</label>
                                <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="nome" placeholder="Ex: #desconto" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Valor mínimo:</label>
                                <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="valor_min" placeholder="Ex: 30,00" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Primeira corrida:</label>
                                <select class="form-control form-control-sm col-md-03 col-sm-03" name="primeira_compra">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Válido até:</label>
                                <br>
                                <input type="date" id="datepicker" name="validade" value="<?php echo date('Y-m-d'); ?>" required />
                                <input type="time" id="time" name="hora">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Quantidade:</label>
                                <input class="form-control form-control-sm col-md-03 col-sm-03" type="number" name="quantidade" placeholder="Ex: 10" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>Um por usuário?</label>
                                <select class="form-control form-control-sm col-md-03 col-sm-03" name="uso_unico">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Forma do desconto:</label>
                                <select class="form-control form-control-sm col-md-03 col-sm-03" id="tipo_desconto" name="tipo_desconto">
                                    <option value="0">Selecione</option>
                                    <option value="1">Porcentagem</option>
                                    <option value="2">Valor</option>
                                </select>
                            </div>
                            <div id="div-porcentagem" class="form-group col-md-3" style="display: none;">
                                <label>Desconto em %:</label>
                                <input class="form-control form-control-sm col-md-03 col-sm-03" type="number" name="valor_porcentagem" placeholder="Ex: 10" />
                            </div>
                            <div id="div-valor" class="form-group col-md-3" style="display: none;">
                                <label>Desconto em R$:</label>
                                <input class="form-control form-control-sm col-md-03 col-sm-03" type="text" name="valor_reais" placeholder="Ex: 7,00" />
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" name="btn_enviar" value="Cadastrar" />
                    </form>
                </div>
                <hr>
                <!--Controlador de tamanho e margem da tabela-->
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="page-header">LISTA DE CUPONS</h4>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-primary btn-sm" style="float: right;" onclick="mostrar_cadastro()">Cadastrar</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <th>Código</th>
                            <th>Ativo</th>
                            <th>Valor</th>
                            <th>Validade</th>
                            <th>Quantidade</th>
                            <th>Ações</th>
                        </thead>
                        <tbody>
                            <?php
                            //Chamando banco de dados
                            foreach ($c->get_cupons_cidade_id($cidade_id) as $linha) {
                                $t->data_fim = $linha['validade'];
                                echo '<tr>';
                                echo  '<td>' . $linha['nome'] . '</td>';
                                if ($t->tempo_passou() < 0 && $linha['quantidade'] > 0) {
                                    echo  '<td><span class="badge badge-success">Sim</span></td>';
                                } else {
                                    echo  '<td><span class="badge badge-danger">Não</span></td>';
                                }
                                echo  '<td>' . $linha['valor'] . '</td>';
                                echo  '<td>' . $t->data_mysql_para_user($linha['validade']) . '</td>';
                                echo  '<td>' . $linha['quantidade'] . '</td>';
                                //Ações                                      
                                echo  "<td>";
                                echo "<a class='btn btn-warning' href='editar_cupons.php?id=$linha[id]' role='button'><i class='bi bi-pencil'></i></a>&nbsp";
                                echo "<a class='btn btn btn-danger' onclick='deletar({$linha['id']})' role='button'><i class='bi bi-trash3'></i></a>&nbsp";
                                echo "</td>";
                                echo "</tr>";
                            ?>

                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--Fechando container bootstrap-->
        <?php include("dep_query.php"); ?>
        <!-- jquery -->
        <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
        <script>
            function deletar(id) {
                if (confirm("Deseja realmente deletar este cupon?")) { 
                    window.location.href = 'deletar.php?id=' + id + '&location=listar_cupons.php&categoria=cupons';
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

            function mostrar_cadastro() {
                let cadastro = document.getElementById("cadastro");
                if (cadastro.style.display == "none") {
                    cadastro.style.display = "block";
                } else {
                    cadastro.style.display = "none";
                }
            }
            $("#tipo_desconto").on("change", function() {

                if ($(this).val() == 1) {
                    $("#div-porcentagem").show();
                    $("#div-valor").hide();
                } else if ($(this).val() == 2) {
                    $("#div-porcentagem").hide();
                    $("#div-valor").show();
                } else {
                    $("#div-porcentagem").hide();
                    $("#div-valor").hide();
                }
            });
        </script>

</body>

</html>