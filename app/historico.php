<!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="theme-color" content="#0b0c0c">
            <title>Ubezap</title>
            <!-- bootstrap css -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <!-- bootstrap icons -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
            <!-- sweetalert -->
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!--material icons-->
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            
            <link rel="manifest" href="manifest.json">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-title" content="Ubezap">
            <meta name="apple-mobile-web-app-status-bar-style" content="default">
            <meta name="msapplication-starturl" content="index.php">
            <link rel="icon" sizes="192x192" href="assets/icon-192x192.png">
            <link rel="apple-touch-icon" href="assets/icon-192x192.png">
            <link rel="shortcut icon" href="assets/icon-192x192.png">
            </head>
        <body>
        <div id="loading-page-bb" style="opacity: 0; height: 100%;">
            <?php
?>


  <div id="cabecalho_msg" class="classe_da_tela" style="background-color: #ffffff; height: 50px; width: 100%;">
    <div id="tela_icon_cabecalho" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 10%;">
      <span class="material-icons" id="icone_voltar" style="font-size:24px; color:#ffffff;">arrow_back</span>
    </div>
    <div id="txt_cabecalho" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 90%;">
      <span class="meu_texto" id="lbl_tela" style="font-size: 18px; color: #ffffff; font-weight: bold; ">Histórico</span>
    </div>
  </div>
  <div id="tela_historico" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
    <div style="width:10px;height:60px;"></div>
  </div>



  <div class="modal fade" id="modal_avaliar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Avaliação da Corrida</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
    <div class="container" id="id_do_container">
      <span class="meu_texto" id="lbl_avaliar" style="font-size: 16px; color: #000000;  ">Que nota você dá para o motorista?</span>
      <div style="width:10px;height:10px;"></div>
      <div class="row justify-content-around" id="Linha">
        <div class="col-2">
          <img src="assets/estrela_cinza.png" height="25px" width="25px" id="estrela_1">
        </div>
        <div class="col-2">
          <img src="assets/estrela_cinza.png" height="25px" width="25px" id="estrela_2">
        </div>
        <div class="col-2">
          <img src="assets/estrela_cinza.png" height="25px" width="25px" id="estrela_3">
        </div>
        <div class="col-2">
          <img src="assets/estrela_cinza.png" height="25px" width="25px" id="estrela_4">
        </div>
        <div class="col-2">
          <img src="assets/estrela_cinza.png" height="25px" width="25px" id="estrela_5">
        </div>
      </div>
      <div style="width:10px;height:20px;"></div>
      <input type="text" class="form-control" id="avaliacao_txt" placeholder="Escreva um comentário..">
    </div>
        </div>
        <div class="modal-footer">
    <button type="button" onclick="enviar_avaliacao()" id="avaliar_btn" class="btn btn-success">Enviar Avaliação</button>
        </div>
      </div>
    </div>
  </div>
<script>

        if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker.register("sw.js").then(function(registration) {
            console.log("ServiceWorker registration successful with scope: ", registration.scope);
            }, function(err) {
            console.log("ServiceWorker registration failed: ", err);
            });
        });
        }

        window.addEventListener("beforeinstallprompt", function(e) {
            console.log("beforeinstallprompt Event fired");
        });

        </script>
            <!-- bootstrap js -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            <!-- jquery -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.js" integrity="sha512-CX7sDOp7UTAq+i1FYIlf9Uo27x4os+kGeoT7rgwvY+4dmjqV0IuE/Bl5hVsjnQPQiTOhAX1O2r2j5bjsFBvv/A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <!-- firebase-app -->
            <script src="https://www.gstatic.com/firebasejs/7.21.0/firebase-app.js"></script>
            <!-- firebase-database -->
            <script src="https://www.gstatic.com/firebasejs/7.21.0/firebase-database.js"></script>
            <!-- firebase-auth -->
            <script src="https://www.gstatic.com/firebasejs/7.15.5/firebase-auth.js"></script>
            <!-- codigo javascript -->
            <script src= "historico.js?v=1.0"> </script>
        </div>
        </body>
        </html>