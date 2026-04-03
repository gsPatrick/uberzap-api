<?php
include "seguranca.php";
include_once '../classes/categorias_horarios.php';
?>
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>

    <body>
        <div class="container-fluid">
        </div>
        <div class="container">
            <div class="container-principal-produtos">
                <h4 class="page-header">CRIAR NOVO DINÂMICO DE HORÁRIOS</h4>
                <hr>
                <form action="cad_cat_horarios.php" method="POST" enctype="multipart/form-data" name="upload">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Nome da categoria:</label>
                            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="nome" required placeholder="ex: madrugada" />
                        </div>
                        <div class="form-group col-md-4">
                            <label>Valor Adicional:</label>
                            <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="adicional" required placeholder="ex: 1,50" />
                        </div>
                    </div>
                    <b>
                    Horários para ativar:
                    </b>
                    <hr>
                    
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Segunda de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="segunda_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="segunda_manha_fim" required value="00:00">
                        </div>

                        <div class="form-group col-md-2">
                            <label>Segunda de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="segunda_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="segunda_noite_fim" required value="00:00">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Terça de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="terca_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="terca_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Terça de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="terca_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="terca_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Quarta de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="quarta_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="quarta_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Quarta de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="quarta_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="quarta_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Quinta de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="quinta_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="quinta_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Quinta de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="quinta_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="quinta_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Sexta de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="sexta_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="sexta_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sexta de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="sexta_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="sexta_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Sábado de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="sabado_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="sabado_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Sábado de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="sabado_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="sabado_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label>Domingo de manhã:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="domingo_manha_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="domingo_manha_fim" required value="00:00">
                        </div>
                        <div class="form-group col-md-2">
                            <label>Domingo de tarde:</label>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="time" id="appt" name="domingo_noite_ini" required value="00:00">
                            <label> fim:</label>
                            <input type="time" id="appt" name="domingo_noite_fim" required value="00:00">
                        </div>
                    </div>
                    <br>
                    <input type="submit" class="btn btn-primary" name="btn_enviar" value="Salvar">
                </form>
                <hr>
            </div>
            <br>
            <hr>
        </div>
        <!--Fechando container bootstrap-->
        <?php include("dep_query.php"); ?>
    </body>

    </html>