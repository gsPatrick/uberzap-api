<!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="theme-color" content="#0b0c0c">
            <title>Uberzap</title>
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
            <meta name="apple-mobile-web-app-title" content="Uberzap">
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


  <div id="loading" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
    <img src="assets/loading.gif" height="100px" width="100px" id="img_loading">
  </div>



  <div class="modal fade" id="add_saldo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Adicionar Saldo</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
    <div class="container" id="id_do_container">
      <span class="meu_texto" id="txt_valor_saldo" style="font-size: 16px; color: #000000;  ">Insira abaixo o valor da recarga:</span>
      <div style="width:10px;height:10px;"></div>
      <input type="text" class="form-control" id="box_valor" placeholder="Ex: 20,00">
    </div>
        </div>
        <div class="modal-footer">
    <button type="button" onclick="iniciar_pagamento()" id="btn_add_saldo" class="btn btn-primary">Recarregar</button>
        </div>
      </div>
    </div>
  </div>



  <div id="tela_cabecalho" class="classe_da_tela" style="background-color: #000000; height: 50px; width: 100%;">
    <span class="meu_texto" id="txt_cabecalho" style="font-size: 18px; color: #ffffff;  ">Carteira de Crédito</span>
  </div>
  <div class="meu_card" id="card_saldo"  style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
    <div class="container" id="id_do_container">
      <span class="meu_texto" id="txt_total" style="font-size: 15px; color: #000000; font-weight: bold; "><span class="material-icons" id="icone_txt_saldo" style="font-size:16px; color:#000000;">account_balance_wallet</span>
      Saldo</span>
      <div id="div_valor" class="minha-div">
        <span class="meu_texto" id="txt_valor" style="font-size: 20px; color: #000000; font-weight: bold; ">R$ 0,00</span>
        <div style="width:10px;height:1px;"></div>
        <span class="material-icons" id="atualizar_saldo" style="font-size:23px; color:#000000;">refresh</span>
      </div>
    </div>
  </div>
  <div style="width:10px;height:10px;"></div>
  <div class="meu_card" id="card_add_saldo"  style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
    <span class="meu_texto" id="txt_add_saldo" style="font-size: 16px; color: #ffffff; font-weight: bold; "><span class="material-icons" id="icone_add_saldo" style="font-size:24px; color:#ffffff;">payments</span>
      Adicionar Saldo </span>
  </div>
  <div style="width:10px;height:10px;"></div>
  <div id="hist_cab" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
    <span class="meu_texto" id="txt_cab_historico" style="font-size: 16px; color: #000000;  ">Historico de Transações:</span>
  </div>
  <div id="historico_transacoes" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
  </div>



  <div class="modal fade" id="modal_detalhes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Detalhes</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
    <div class="container" id="id_do_container">
      <span class="meu_texto" id="txt_detalhes" style="font-size: 16px; color: #000000;  ">Sem Detalhes</span>
      <span class="meu_texto" id="btn_pagar_fatura" style="font-size: 16px; color: #000099;  font-style: italic;"><br>Pagar Fatura</span>
    </div>
        </div>
        <div class="modal-footer">
    <button type="button" onclick="fechar_modal_detalhe()" id="btn_fx_modal" class="btn btn-primary">Fechar</button>
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
            <script src= "carteira.js?v=1.0"> </script>
        </div>
        </body>
        </html>