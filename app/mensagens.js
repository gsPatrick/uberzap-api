var lista, id_usuario, telefone_cliente, posicao, senha_cliente, url_principal, global_lista_mensagens, temporizador_1, Item, tamanho_anterior, nome_usuario;

// Descreva esta função...
function busca_msgs() {
  ajax_post_async((String(url_principal) + 'get_mensagens.php'), {telefone:telefone_cliente, senha:senha_cliente}, listar_mensagens);
}

// Descreva esta função...
function enviar_msg() {
  ajax_post_async((String(url_principal) + 'insere_msg.php'), {telefone:telefone_cliente, senha:senha_cliente, msg:document.getElementById('msg-box').value}, busca_msgs);
  $("#msg-box").val('');
}

// Descreva esta função...
function listar_mensagens(lista) {
  limpar_msgs();
  posicao = 0;
  if (lista) {
    global_lista_mensagens = JSON.parse(lista);
    posicao = posicao + 1;
    for (var Item_index in global_lista_mensagens) {
      Item = global_lista_mensagens[Item_index];
      nome_usuario = [Item['sender'] == 2 ? String('<span style="font-size:14px; color:#009900; font-weight:bold; font-style:italic;">'+'Você:'+' </span>') + '<br>' : String('<span style="font-size:14px; color:#ff6600; font-weight:bold; font-style:italic;">'+'Motorista: '+' </span>') + '<br>','<span style="font-size:18px; color:#000000; font-weight:normal; font-style:normal;">'+Item['msg']+' </span>','<br>','<span style="font-size:12px; color:#333333; font-weight:normal; font-style:normal;">'+Item['date']+' </span>'].join('');
      if (Item['sender'] == 2) {
        document.getElementById('mensagens').innerHTML += '<div class='+'msg_remetente'+' id='+'msg_rem'+' onclick="responder('+posicao+')" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">'+nome_usuario+'</div>';
      } else {
        document.getElementById('mensagens').innerHTML += '<div class='+'msg_destinatario'+' id='+'msg_dest'+' onclick="responder('+posicao+')" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">'+nome_usuario+'</div>';
      }
    }
  } else {
    document.getElementById('mensagens').innerHTML += '<div class='+'msg'+' id='+'msg'+' onclick="nenhuma('+0+')" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">'+'Nenhuma Mensagem '+'</div>';
  }
  $(".msg_remetente").css("text-align", "right");
  $(".msg_remetente").css("height", '' + "px");
  $(".msg_remetente").css("width", '80' + "%");
  $("."+'msg_remetente').css("margin-left", (window.innerWidth * (18 / 100))+"px");
  $("."+'msg_remetente').css("margin-right", 0+"px");
  $("."+'msg_remetente').css("margin-top", 10+"px");
  $("."+'msg_remetente').css("margin-bottom", 0+"px");
  $("."+'msg_destinatario').css("margin-left", 0+"px");
  $("."+'msg_destinatario').css("margin-right", 0+"px");
  $("."+'msg_destinatario').css("margin-top", 10+"px");
  $("."+'msg_destinatario').css("margin-bottom", 0+"px");
  $("."+'msg_remetente').css("padding-left", 10+"px");
  $("."+'msg_remetente').css("padding-right", 10+"px");
  $("."+'msg_remetente').css("padding-top", 10+"px");
  $("."+'msg_remetente').css("padding-bottom", 10+"px");
  $("."+'msg_destinatario').css("padding-left", 10+"px");
  $("."+'msg_destinatario').css("padding-right", 10+"px");
  $("."+'msg_destinatario').css("padding-top", 10+"px");
  $("."+'msg_destinatario').css("padding-bottom", 10+"px");
  $(".msg_destinatario").css("height", '' + "px");
  $(".msg_destinatario").css("width", '80' + "%");
  $(".msg_destinatario").css("border-radius", "20px");
  $(".msg_remetente").css("border-radius", "20px");
  if (global_lista_mensagens.length > tamanho_anterior) {
    tamanho_anterior = global_lista_mensagens.length;
    let elmnt = document.getElementById('mensagens');
    elmnt.scrollIntoView(false);
    console.log((global_lista_mensagens.length));
  }
}

// Descreva esta função...
function limpar_msgs() {
  function removeElementsByClass(className){
      var elements = document.getElementsByClassName(className);
      while(elements.length > 0){
          elements[0].parentNode.removeChild(elements[0]);
      }
  }
  removeElementsByClass('msg_remetente');
  function removeElementsByClass(className){
      var elements = document.getElementsByClassName(className);
      while(elements.length > 0){
          elements[0].parentNode.removeChild(elements[0]);
      }
  }
  removeElementsByClass('msg_destinatario');
}


//feito com bootblocks.com.br
  $("#"+'mensagens').css("padding-left", 0+ "px");
  $("#"+'mensagens').css("padding-right", 0+ "px");
  $("#"+'mensagens').css("padding-top", 60+ "px");
  $("#"+'mensagens').css("padding-bottom", 100+ "px");
  $("#"+'mensagens').css("margin-left", 0+ "px");
  $("#"+'mensagens').css("margin-right", 0+ "px");
  $("#"+'mensagens').css("margin-top", 0+ "px");
  $("#"+'mensagens').css("margin-bottom", 50+ "px");
  $("#"+'subtela_mensagens').css("padding-left", 0+ "px");
  $("#"+'subtela_mensagens').css("padding-right", 0+ "px");
  $("#"+'subtela_mensagens').css("padding-top", 0+ "px");
  $("#"+'subtela_mensagens').css("padding-bottom", 50+ "px");
  $("#"+'lbl_tela').css("padding-left", 20+ "px");
  $("#"+'lbl_tela').css("padding-right", 0+ "px");
  $("#"+'lbl_tela').css("padding-top", 0+ "px");
  $("#"+'lbl_tela').css("padding-bottom", 0+ "px");
   function makeLoading(){
  let loading = '<div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0); z-index: 9999;"><img id="loading-image" src="assets/loading.gif" alt="Carregando..." style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div>'
  document.body.innerHTML += loading;
  }
   makeLoading();

//feito com bootblocks.com.br
  id_usuario = localStorage.getItem('cliente_id') || '';
  telefone_cliente = localStorage.getItem('telefone_cliente') || '';
  senha_cliente = localStorage.getItem('senha_cliente') || '';
  url_principal = localStorage.getItem('url_principal') || '';
  busca_msgs();
  temporizador_1 = setInterval(function(){
    busca_msgs();
  }, 1000);
  tamanho_anterior = 0;

//feito com bootblocks.com.br
  $("body").css("width", "100%");
  $("html").css("width", "100%");
  document.getElementById('cabecalho_msg').style.position = "fixed";
  document.getElementById('cabecalho_msg').style.top = "0px";
  document.getElementById('cabecalho_msg').style.left = "0";
  document.getElementById('cabecalho_msg').style.right = "0";
  document.getElementById('cabecalho_msg').style.zIndex = "20";
  $("#"+'cabecalho_msg').css("padding-left", 10+ "px");
  $("#"+'cabecalho_msg').css("padding-right", 0+ "px");
  $("#"+'cabecalho_msg').css("padding-top", 10+ "px");
  $("#"+'cabecalho_msg').css("padding-bottom", 0+ "px");
  $("#cabecalho_msg").css("display", "flex");
  $("#cabecalho_msg").css("justify-content", "center");
  $("#tela_icon_cabecalho").css("background-color", "#0b0c0c");
  $("#cabecalho_msg").css("background-color", "#0b0c0c");
  $("#txt_cabecalho").css("background-color", "#0b0c0c");
  document.getElementById('nova_msg').style.position = "fixed";
  document.getElementById('nova_msg').style.bottom = "0px";
  document.getElementById('nova_msg').style.left = "0";
  document.getElementById('nova_msg').style.right = "0";
  document.getElementById('nova_msg').style.zIndex = "20";
  $("#"+'nova_msg').css("margin-left", 0+ "px");
  $("#"+'nova_msg').css("margin-right", 0+ "px");
  $("#"+'nova_msg').css("margin-top", 0+ "px");
  $("#"+'nova_msg').css("margin-bottom", 10+ "px");
  $("#"+'nova_msg').css("padding-left", 0+ "px");
  $("#"+'nova_msg').css("padding-right", 10+ "px");
  $("#"+'nova_msg').css("padding-top", 0+ "px");
  $("#"+'nova_msg').css("padding-bottom", 0+ "px");
  $("#"+'card_msg').css("padding-left", 0+ "px");
  $("#"+'card_msg').css("padding-right", 0+ "px");
  $("#"+'card_msg').css("padding-top", 0+ "px");
  $("#"+'card_msg').css("padding-bottom", 0+ "px");
  document.getElementById('msg-box').style.border = 0 + "px solid " + "#cccccc";
  $("#nova_msg").css("display", "flex");
  $("#nova_msg").css("align-items", "center");
  $("#"+'enviar_msg').css("margin-left", 0+ "px");
  $("#"+'enviar_msg').css("margin-right", 10+ "px");
  $("#"+'enviar_msg').css("margin-top", 10+ "px");
  $("#"+'enviar_msg').css("margin-bottom", 0+ "px");
  $("#msg-box").css("border-radius", "15px");
  $("#card_msg").css("border-radius", "15px");
  $("#nova_msg").css("background-color", "rgba(0, 0, 0, 0)");

//feito com bootblocks.com.br


        function qclick() {
            let elementoClick = document.getElementById('icone_voltar');
            if (elementoClick) {
                elementoClick.addEventListener("click", function () {
                      window.location.href = "home.php";
                });
            }
        }
        qclick();


        function qclick2() {
            let elementoClick = document.getElementById('enviar_msg');
            if (elementoClick) {
                elementoClick.addEventListener("click", function () {
                      if (document.getElementById('msg-box').value) {
    enviar_msg();
  }

                });
            }
        }
        qclick2();
function ajax_post(url, dados){
                let retorno;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: dados,
                    async: false,
                    success: function(data){
                        retorno = data;
                    },
                    error: function(data){
                        retorno = data;
                    }
                });
                return retorno;
            }function ajax_post_async(url, dados, funcao_chamar){
                $.ajax({
                    url: url,
                    type: "POST",
                    data: dados,
                    async: true,
                    success: function(data){
                        funcao_chamar(data);
                    },
                    error: function(data){
                        funcao_chamar(data);
                    }
                });
            }
            
        $(document).ready(function(){
            $("#loading-page-bb").css("opacity", "1");
        });