<?php
include("seguranca.php");
include_once("../classes/categorias_horarios.php");
$ch = new dinamico_horarios();
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
        <?php

        $dados = $ch->get_dinamico_horarios_id($_GET['id'])[0];
        $domingo = unserialize($dados['domingo']);
        $segunda = unserialize($dados['segunda']); 
        $terca = unserialize($dados['terca']);
        $quarta = unserialize($dados['quarta']);
        $quinta = unserialize($dados['quinta']);
        $sexta = unserialize($dados['sexta']);
        $sabado = unserialize($dados['sabado']);
        $nome = $dados['nome'];

        ?>
    </div>
    <div class="container">
        <div class="container-principal-produtos">
            <h4 class="page-header">EDITAR CATEGORIA DE HORÁRIOS</h4>
            <hr>
            <form action="edit_din_horario.php?id=<?php echo $_GET['id'];?>" method="POST" enctype="multipart/form-data" name="upload">
            <div class="row">
                        <div class="form-group col-md-4">
                            <label>Nome da categoria:</label>
                            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="nome" required placeholder="ex: madrugada" value="<? echo $nome; ?>" />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Valor Adicional:</label>
                            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="adicional" required placeholder="ex: 1,50" value="<? echo $dados['adicional']; ?>" />
                        </div>
                    </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Segunda de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="segunda_manha_ini" value="<? echo $segunda['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="segunda_manha_fim" value="<? echo $segunda['manha_fim']; ?>" required>
                    </div>

                    <div class="form-group col-md-2">
                        <label>Segunda de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="segunda_noite_ini" value="<? echo $segunda['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="segunda_noite_fim" value="<? echo $segunda['noite_fim']; ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Terça de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="terca_manha_ini" value="<? echo $terca['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="terca_manha_fim" value="<? echo $terca['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Terça de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="terca_noite_ini" value="<? echo $terca['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="terca_noite_fim" value="<? echo $terca['noite_fim']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Quarta de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="quarta_manha_ini" value="<? echo $quarta['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="quarta_manha_fim" value="<? echo $quarta['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Quarta de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="quarta_noite_ini" value="<? echo $quarta['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="quarta_noite_fim" value="<? echo $quarta['noite_fim']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Quinta de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="quinta_manha_ini" value="<? echo $quinta['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="quinta_manha_fim" value="<? echo $quinta['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Quinta de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="quinta_noite_ini" value="<? echo $quinta['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="quinta_noite_fim" value="<? echo $quinta['noite_fim']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Sexta de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="sexta_manha_ini" value="<? echo $sexta['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="sexta_manha_fim" value="<? echo $sexta['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Sexta de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="sexta_noite_ini" value="<? echo $sexta['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="sexta_noite_fim" value="<? echo $sexta['noite_fim']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Sábado de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="sabado_manha_ini" value="<? echo $sabado['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="sabado_manha_fim" value="<? echo $sabado['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Sábado de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="sabado_noite_ini" value="<? echo $sabado['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="sabado_noite_fim" value="<? echo $sabado['noite_fim']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Domingo de manhã:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="domingo_manha_ini" value="<? echo $domingo['manha_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="domingo_manha_fim" value="<? echo $domingo['manha_fim']; ?>" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Domingo de tarde:</label>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="time" id="appt" name="domingo_noite_ini" value="<? echo $domingo['noite_ini']; ?>" required>
                        <label> fim:</label>
                        <input type="time" id="appt" name="domingo_noite_fim" value="<? echo $domingo['noite_fim']; ?>" required>
                    </div>
                </div>
                <br>
                <input type="submit" class="btn btn-primary" name="btn_enviar" value="Salvar">
            </form>
        </div>
        <br>
        <hr>
    </div>
    <!--Fechando container bootstrap-->
    <?php include("dep_query.php"); ?>
</body>

</html>