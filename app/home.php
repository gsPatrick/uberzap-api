<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#1f2020">
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
    <audio id="audio" src="assets/toque_status.mp3" controls></audio>
    <audio id="audio_message" src="assets/messenger.mp3" controls></audio>

    <div class="banners_rotativos" id="banners_rotativos" style="display: none; position: fixed; top: 60px; left: 50%; transform: translateX(-50%); width: 96%; height: auto; z-index: 24;">
    </div>



    <div id="tela_status" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      <div id="tela_cabecalho_status" class="classe_da_tela" style="background-color: #ffffff; height: 50px; width: 100%;">
        <div style="width:90%;height:10px;"></div>
        <span class="material-icons" id="icone_minimizar" style="font-size:27px; color:#333333;">close_fullscreen</span>
      </div>
      <div id="tela_img_motorista" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <img src="" style="height: 100px; width: 100px;" id="img_motorista">
      </div>
      <div id="tela_dados_motorista" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="dados_motorista" style="font-size: 16px; color: #000000;  "></span>
        <div style="width:10px;height:10px;"></div>
      </div>
      <div id="tela_lottie" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <!-- Reprodutor Lottie -->
        <lottie-player id="reprodutor_lottie_1" src="assets/procurando.json" background="transparent" speed="1" style="width: 200px; height: 100px" direction="1" autoplay mode="normal" loop></lottie-player>
        <!-- Reprodutor Lottie -->
        <lottie-player id="reprodutor_lottie_2" src="assets/em_andamento.json" background="transparent" speed="1" style="width: 250px; height: 150px" direction="1" autoplay mode="normal" loop></lottie-player>
        <!-- Reprodutor Lottie -->
        <lottie-player id="reprodutor_lottie_3" src="assets/finalizada.json" background="transparent" speed="1" style="width: 250px; height: 150px" direction="1" autoplay mode="normal" loop></lottie-player>
        <!-- Reprodutor Lottie -->
        <lottie-player id="reprodutor_lottie_4" src="assets/cancelada.json" background="transparent" speed="1" style="width: 250px; height: 150px" direction="1" autoplay mode="normal" loop></lottie-player>
      </div>
      <div id="tela_status_txt" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="txt_status" style="font-size: 18px; color: #333333; font-weight: bold; ">Procurando Motorista</span>
      </div>
      <div id="tela_info_status" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%; display: none; align-items: center; justify-content: center;">
        <span class="meu_texto" id="txt_info_status" style="font-size: 16px; color: #000000;"></span>
      </div>
      <div style="width:10px;height:10px;"></div>
      <div id="tela_timer" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="txt_timer" style="font-size: 30px; color: #333333; font-weight: bold; ">0:00</span>
      </div>
      <div style="width:10px;height:10px;"></div>
      <div id="tela_txt_finalizar" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="txt_total_fim" style="font-size: 16px; color: #000000;  "></span>
      </div>
      <div id="tela_botoes_status" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <div class="meu_card" id="card_cancelar" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="txt_cancelar" style="font-size: 18px; color: #000000; font-weight: bold; ">Cancelar Corrida</span>
        </div>
        <div class="meu_card" id="card_finalizar" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="txt_finalizar" style="font-size: 18px; color: #000000; font-weight: bold; ">Finalizar</span>
        </div>
        <div class="meu_card" id="tentar_novamente" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="txt_tentar_novamente" style="font-size: 18px; color: #000000; font-weight: bold; ">Tentar Novamente</span>
        </div>
      </div>
      <div style="width:10px;height:10px;"></div>
    </div>



    <div class="modal fade" id="modal_dados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Meus Dados</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container" id="id_do_container">
              <span class="meu_texto" id="txt_nome_telefone_dados" style="font-size: 16px; color: #000000;  "></span>
              <div style="width:10px;height:15px;"></div>
              <span class="meu_texto" id="altera_senha" style="font-size: 17px; color: #000000; font-weight: bold; "><?php echo "<span style='color:#000099;'>" . '<br> Alterar Senha:' . "</span>"; ?></span>
              <input type="text" class="form-control" id="dados_senha_1" placeholder="Nova Senha">
              <div style="width:10px;height:10px;"></div>
              <input type="text" class="form-control" id="dados_senha_2" placeholder="Repita Nova Senha">
              <div style="width:10px;height:10px;"></div>
              <button type="button" onclick="alterar_senha()" id="btn_alterar_senha" class="btn btn-primary">Alterar Senha</button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="fechar_modal_dados()" id="btn_fechar_modal_dados" class="btn btn-secondary">Fechar</button>
          </div>
        </div>
      </div>
    </div>



    <div class="modal fade" id="modal_contato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Contato</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container" id="id_do_container">
              <span class="meu_texto" id="txt_contato" style="font-size: 16px; color: #000000;  "></span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="fechar_modal()" id="fechar_modal_btn" class="btn btn-secondary">Fechar</button>
            <button type="button" onclick="enviar_whats_contato()" id="whats_btn" class="btn btn-success">Whatsapp</button>
          </div>
        </div>
      </div>
    </div>


    <?php
    ?>


    <div id="tela_menu" class="classe_da_tela" style="background-color: #ffffff; height: 100%; width: 100%;">
      <div class="container" id="container_menu">
        <div style="width:10px;height:100px;"></div>
        <div id="cabecalho_menu" class="classe_da_tela" style="background-color: #ffffff; height: 50px; width: 100%;">
          <span class="meu_texto" id="lbl_nome_cliente_menu" style="font-size: 20px; color: #000000; font-weight: bold; "></span>
        </div>
        <div style="width:10px;height:15px;"></div>
        <div id="tela_carteira_credito" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
          <span class="material-icons" id="icone_carteira_credito" style="font-size:24px; color:#000000;">account_balance_wallet</span>
          <div style="width:10px;height:1px;"></div>
          <span class="meu_texto" id="lbl_crt_credito" style="font-size: 18px; color: #000000;  ">Carteira de Crédito</span>
        </div>
        <div style="width:10px;height:15px;"></div>
        <div id="tela_meus_dados" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
          <span class="material-icons" id="icone_meus_dados" style="font-size:24px; color:#000000;">manage_accounts</span>
          <div style="width:10px;height:1px;"></div>
          <span class="meu_texto" id="lbl_meus_dados" style="font-size: 18px; color: #000000;  ">Meus Dados</span>
        </div>
        <div style="width:10px;height:15px;"></div>
        <div id="tela_historico_corridas" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
          <span class="material-icons" id="icone_historico_corridas" style="font-size:24px; color:#000000;">history</span>
          <div style="width:10px;height:1px;"></div>
          <span class="meu_texto" id="lbl_historico_corridas" style="font-size: 18px; color: #000000;  ">Histórico de Corridas</span>
        </div>
        <div style="width:10px;height:15px;"></div>
        <div id="tela_fale_conosco" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
          <span class="material-icons" id="icone_fale_conosco" style="font-size:24px; color:#000000;">call</span>
          <div style="width:10px;height:1px;"></div>
          <span class="meu_texto" id="lbl_fale_conosco" style="font-size: 18px; color: #000000;  ">Fale Conosco</span>
        </div>
        <div style="width:10px;height:15px;"></div>

        <!-- Novo item adicionado: Política de Privacidade -->
        <div id="tela_politica_privacidade" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%; display: flex; align-items: center;">
          <a href="https://top.uberzap.app.br/politica-de-privacidade.html" target="_blank" style="text-decoration: none; display: flex; align-items: center;">
            <span class="material-icons" id="icone_politica_privacidade" style="font-size:24px; color:#000000;">policy</span>
            <span class="meu_texto" id="lbl_politica_privacidade" style="font-size: 18px; color: #000000; margin-left: 10px;">Política de Privacidade</span>
          </a>
        </div>
        <div style="width:10px;height:15px;"></div>

        <div id="tela_sair" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
          <span class="material-icons" id="icone_sair" style="font-size:24px; color:#000000;">logout</span>
          <div style="width:10px;height:1px;"></div>
          <span class="meu_texto" id="lbl_sair" style="font-size: 18px; color: #000000;  ">Sair</span>
        </div>
      </div>
    </div>



    <div id="tela_mapa" class="classe_da_tela" style="background-color: #ffffff; height: 100%; width: 100%;">
    </div>



    <div id="cabecalho" class="classe_da_tela" style="background-color: #000000; height: 50px; width: 100%;">
      <div id="tela_icone_menu" class="classe_da_tela" style="background-color: #000000; height: auto; width: 10%;">
        <span class="material-icons" id="icone_menu" style="font-size:27px; color:#ffffff;">menu</span>
      </div>
      <div id="tela_label_cabecalho" class="classe_da_tela" style="background-color: #000000; height: auto; width: 90%;">
        <span class="meu_texto" id="txt_cabecalho" style="font-size: 20px; color: #ffffff; font-weight: bold; ">Ubezap</span>
      </div>
      <div id="tela_icone_chat" class="classe_da_tela" style="background-color: #333333; height: auto; width: 10%;">
        <span class="material-icons" id="icone_chat" style="font-size:27px; color:#ffffff;">chat</span>
      </div>
    </div>



    <div id="tela_barra_inicio" class="classe_da_tela" style="background-color: #ffffff; height: 10%; width: 100%;">
      <div id="tela_boas_vindas" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="lbl_boas_vindas" style="font-size: 17px; color: #000000; font-weight: bold; "></span>
      </div>
      <div id="tela_onde_vamos" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <span class="meu_texto" id="lbl_onde_vamos" style="font-size: 10px; color: #3366ff;  ">Para onde vamos?</span>
      </div>
      <div style="width:10px;height:10px;"></div>
      <div id="tela_card_iniciar" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <div class="meu_card" id="card_iniciar" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="lbl_onde_card_iniciar" style="font-size: 16px; color: #ffffff; font-weight: bold; ">Escolher Destino</span>
          <span class="material-icons" id="icone_arrow" style="font-size:24px; color:#ffffff;">arrow_outward</span>
        </div>
      </div>
    </div>



    <div id="tela_categorias" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      <div id="tela_cabecalho_categorias" class="classe_da_tela" style="background-color: #ffffff; height: 50px; width: 100%;">
        <div id="tela_lbl_categoria" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">

          <!-- Botão de voltar com a seta circular preta -->
          <span class="material-icons" id="btn_voltar" style="font-size:25px; color:#000000; cursor:pointer; margin-right: 10px;">arrow_back</span>

          <div class="container" id="id_do_container">
            <span class="meu_texto" id="txt_tela_categorias" style="font-size: 16px; color: #000000; font-weight: bold; ">Escolha o tipo de viagem:</span>
          </div>
        </div>
        <span class="material-icons" id="icone_minimizar_categorias" style="font-size:27px; color:#333333;">close_fullscreen</span>
      </div>
      <div id="tela_lista_categorias" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      </div>
      <div style="width:10px;height:10px;"></div>
      <div class="container" id="id_do_container">
        <span class="meu_texto" id="lbl_f_pagamento" style="font-size: 16px; color: #000000;  ">Forma de Pagamento:</span>
        <?php
        $i = 0;
        foreach ((array('Dinheiro ou Pix', 'Carteira Crédito', 'Cartão Máquina')) as $elemento) {
          $i = $i + 1; ?>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="forma_pagamento" id="forma_pagamento_<?php echo $i; ?>" value="<?php echo $elemento; ?>">
            <label class="form-check-label" for="forma_pagamento_<?php echo $i; ?>"><?php echo $elemento; ?></label>
          </div>
        <?php } ?>
        
        <!-- cupom de desconto -->
        <div class="input-group mb-1">
          <input type="text" class="form-control" id="cupom_desconto_input" placeholder="Cupom de desconto" aria-label="Cupom de desconto">
          <button class="btn btn-primary btn-sm" type="button" id="btn_aplicar_cupom">Aplicar</button>
        </div>
        <span class="meu_texto" id="lbl_status_cupom" style="font-size: 16px; color: #008000;"></span>
        <div style="width:10px;height:10px;"></div>
      </div>
      <div id="tela_card_iniciar_chamado" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <div class="meu_card" id="card_iniciar_chamado" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="txt_card_chamado" style="font-size: 16px; color: #ffffff; font-weight: bold; "></span>
        </div>
      </div>
      <div style="width:10px;height:5px;"></div>
    </div>



    <div id="loading" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      <img src="assets/loading.gif" height="100px" width="100px" id="img_loading">
    </div>



    <div id="tela_destinos" class="classe_da_tela" style="background-color: #ffffff; height: 100%; width: 100%;">
      <div class="container" id="id_do_container">
        <div id="destinos_cabecalho" class="classe_da_tela" style="background-color: #ffffff; height: 50px; width: 100%;">
          <span class="material-icons" id="icone_voltar_destinos" style="font-size:25px; color:#000000;">arrow_back</span>
          <div id="tela_lbl_destino" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 80%;">
            <span class="meu_texto" id="lbl_selecionar_destino" style="font-size: 16px; color: #000000;  ">Selecionar Destino</span>
          </div>
        </div>
        <input type="text" class="form-control" id="box_origem" placeholder="Partida">
        <div style="width:10px;height:5px;"></div>
        
        <!-- 📍 Escolher origem no Mapa-->
        <span class="meu_texto" id="escolher_mapa_origem" style="font-size: 16px; color: #000000;  ">📍 Escolher origem no Mapa</span>
		
        <div style="width:10px;height:10px;"></div>
        <input type="text" class="form-control" id="box_destino" placeholder="Destino">
        <div style="width:10px;height:10px;"></div>
		<!-- 📍 Escolher destino no Mapa-->
        <span class="meu_texto" id="escolher_mapa_destino" style="font-size: 16px; color: #000000;  ">📍 Escolher destino no Mapa</span>
		
        <!-- <div style="width:10px;height:10px;"></div>
        <textarea class="form-control" id="box_obs" placeholder="Observações" rows="3"></textarea>
        <div style="width:10px;height:10%;"></div> -->
      </div>
    </div>



    <div id="tela_btn_avancar" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      <div class="meu_card" id="btn_avancar" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
        <span class="meu_texto" id="lbl_avancar" style="font-size: 16px; color: #ffffff;  ">Avançar</span>
      </div>
    </div>

    <div id="tela_base_escolha" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
      <div class="container" id="id_do_container">
        <div id="div_1" class="text-center">
          <span class="meu_texto" id="endereco_escolhido_mapa" style="font-size: 16px; color: #000000;  ">Arraste o pino para selecionar um endereço</span>
        </div>
      </div>
      <div style="width:10px;height:10px;"></div>
      <div id="tela_btn_avancar_escolha" class="classe_da_tela" style="background-color: #ffffff; height: auto; width: 100%;">
        <div class="meu_card" id="btn_avancar_escolha" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">
          <span class="meu_texto" id="lbl_avancar_escolha" style="font-size: 16px; color: #ffffff;  ">Pronto</span>
        </div>
      </div>
      <div style="width:10px;height:10px;"></div>
    </div>
    <script>
      // Função para o botão de voltar usando o histórico do navegador
      document.getElementById("btn_voltar").addEventListener("click", function() {
        window.history.back();
      });

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
    <!-- Lottie files -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- codigo javascript -->
    <script src="home.js?v=<?php echo time(); ?>">
    </script>
  </div>
</body>

</html>