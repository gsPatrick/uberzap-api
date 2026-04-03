var retorno_atualizacao, ret, dados_carteira, id_hs, url_pagamento, link_pagamento, largura_da_tela, transacoes, link_fatura, token_mp, nome_cliente, url_principal, contador, latitude_usuario, cidade_id, cliente_id, valor_recarga, longitude_usuario, item_transacao, referencia_pagamento, token, url_do_app, senha, telefone;

// Descreva esta função... esta é a versão restaurada
function iniciar_pagamento() {
  url_pagamento = '';
  if (token_mp) {
    if ($("#box_valor").val()) {
      $("#add_saldo").modal("hide");
      $("#"+'loading').show();
      valor_recarga = $("#box_valor").val();
      function get_link_mp(token, descricao, valor, url_sucesso, url_falha, url_pendente) {
      var url = "https://api.pagmp.com/api/get_link.php";
      var data = {
      token: token,
      descricao: descricao,
      valor: valor,
      url_sucesso: url_sucesso,
      url_falha: url_falha,
      url_pendente: url_pendente
      };
      $.ajax({
      type: "POST",
      url: url,
      data: data,
      success: function(data) {
      let json = JSON.parse(data);
      if (json.status == "successo") {
      var link_pagamento = json.url;
      var referencia_pagamento = json.ref;
        url_pagamento = link_pagamento;
        ajax_post_async((String(url_principal) + 'insere_transacao.php'), {cidade_id:cidade_id, user_id:cliente_id, ref:referencia_pagamento, valor:valor_recarga, link:link_pagamento}, finaliza_pagamento);
      }
      }
      });
      }
      get_link_mp(token_mp, 'Recarga Saldo', valor_recarga, url_do_app, url_do_app, url_do_app);
    } else {
      Swal.fire('Digite um valor');
    }
  } else {
    Swal.fire('Cadastre o Token do Mercado Pago no Painel');
  }
}

// Descreva esta função...
function finalizar_atualizacao(retorno_atualizacao) {
  retorno_atualizacao = JSON.parse(retorno_atualizacao);
  console.log(retorno_atualizacao);
  if (retorno_atualizacao['status'] == 'ok') {
    window.location.href = "carteira.php";} else {
    $("#"+'loading').hide();
    Swal.fire('Atualizado!');
  }
}

// Descreva esta função...
function finaliza_pagamento(ret) {
  window.location.href = url_pagamento;
}

// Descreva esta função...
function fechar_modal_detalhe() {
  $("#modal_detalhes").modal("hide");
}

// Descreva esta função...
function preencher_saldo(dados_carteira) {
  dados_carteira = JSON.parse(dados_carteira);
  transacoes = dados_carteira['transacoes'];
  $("#txt_valor").html(('R$ ' + String(dados_carteira['saldo'])));
  contador = 0;
  if (!!transacoes.length) {
    for (var item_transacao_index in transacoes) {
      item_transacao = transacoes[item_transacao_index];
      contador = contador + 1;
      document.getElementById('historico_transacoes').innerHTML += '<div class='+'lista_historico'+' id='+'h_t'+' onclick="detalhar_historico('+contador+')" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">'+(['<span style="font-size:13px; color:#333333; font-weight:bold; font-style:normal;">'+item_transacao['date']+' </span>','<br>','CREDITO PLATAFORMA' == item_transacao['metodo'] || 'RECARGA ONLINE' == item_transacao['metodo'] ? '<span style="font-size:15px; color:#009900; font-weight:bold; font-style:normal;">'+item_transacao['metodo']+' </span>' : '<span style="font-size:15px; color:#ff0000; font-weight:bold; font-style:normal;">'+item_transacao['metodo']+' </span>','CREDITO PLATAFORMA' == item_transacao['metodo'] || 'RECARGA ONLINE' == item_transacao['metodo'] ? '<span style="font-size:15px; color:#009900; font-weight:bold; font-style:normal;">'+('R$ ' + String(item_transacao['valor']))+' </span>' : '<span style="font-size:15px; color:#ff0000; font-weight:bold; font-style:normal;">'+('R$ ' + String(item_transacao['valor']))+' </span>','<br> Status: ','CONCLUIDO' == item_transacao['status'] ? '<span style="font-size:14px; color:#009900; font-weight:normal; font-style:italic;">'+item_transacao['status']+' </span>' : '<span style="font-size:14px; color:#ffcc00; font-weight:normal; font-style:italic;">'+item_transacao['status']+' </span>'].join(''))+'</div>';
    }
  }
  $("."+'lista_historico').css("padding-left", 10+"px");
  $("."+'lista_historico').css("padding-right", 0+"px");
  $("."+'lista_historico').css("padding-top", 0+"px");
  $("."+'lista_historico').css("padding-bottom", 0+"px");
  $("."+'lista_historico').css("margin-left", 0+"px");
  $("."+'lista_historico').css("margin-right", 0+"px");
  $("."+'lista_historico').css("margin-top", 0+"px");
  $("."+'lista_historico').css("margin-bottom", 5+"px");
  $("#"+'loading').hide();
}

// Descreva esta função...
function detalhar_historico(id_hs) {
  console.log(id_hs);
  $("#txt_detalhes").html((['CREDITO PLATAFORMA' == (transacoes[(id_hs - 1)])['metodo'] || 'RECARGA ONLINE' == (transacoes[(id_hs - 1)])['metodo'] ? '<span style="font-size:16px; color:#009900; font-weight:normal; font-style:normal;">'+(transacoes[(id_hs - 1)])['metodo']+' </span>' : '<span style="font-size:16px; color:#ff0000; font-weight:normal; font-style:normal;">'+(transacoes[(id_hs - 1)])['metodo']+' </span>','<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">'+'<br> Valor:'+' </span>','CREDITO PLATAFORMA' == (transacoes[(id_hs - 1)])['metodo'] || 'RECARGA ONLINE' == (transacoes[(id_hs - 1)])['metodo'] ? '<span style="font-size:16px; color:#009900; font-weight:normal; font-style:normal;">'+(transacoes[(id_hs - 1)])['valor']+' </span>' : '<span style="font-size:16px; color:#ff0000; font-weight:normal; font-style:normal;">'+(transacoes[(id_hs - 1)])['valor']+' </span>','<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">'+'<br> Data:'+' </span>','<span style="font-size:16px; color:#000000; font-weight:normal; font-style:italic;">'+(transacoes[(id_hs - 1)])['date']+' </span>','<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">'+'<br> Status:'+' </span>','CONCLUIDO' == (transacoes[(id_hs - 1)])['status'] ? '<span style="font-size:16px; color:#009900; font-weight:bold; font-style:normal;">'+(transacoes[(id_hs - 1)])['status']+' </span>' : '<span style="font-size:16px; color:#ffcc00; font-weight:bold; font-style:normal;">'+(transacoes[(id_hs - 1)])['status']+' </span>'].join('')));
  $("#modal_detalhes").modal("show");
  if ('' != (transacoes[(id_hs - 1)])['link'] && 'PENDENTE' == (transacoes[(id_hs - 1)])['status']) {
    $("#"+'btn_pagar_fatura').show();
    link_fatura = (transacoes[(id_hs - 1)])['link'];
    console.log(link_fatura);
  } else {
    $("#"+'btn_pagar_fatura').hide();
  }
}


//feito com bootblocks.com.br
  $("#"+'txt_cab_historico').css("margin-left", 10+ "px");
  $("#"+'txt_cab_historico').css("margin-right", 0+ "px");
  $("#"+'txt_cab_historico').css("margin-top", 0+ "px");
  $("#"+'txt_cab_historico').css("margin-bottom", 10+ "px");

//feito com bootblocks.com.br
  $("#loading").css("background-color", "rgba(0, 0, 0, 0)");
  $("#loading").css("display", "flex");
  $("#loading").css("justify-content", "center");
  document.getElementById('loading').style.position = "fixed";
  document.getElementById('loading').style.top = "50%";
  document.getElementById('loading').style.transform = "translateY(-50%)";
  document.getElementById('loading').style.left = "0";
  document.getElementById('loading').style.right = "0";
  document.getElementById('loading').style.zIndex = "20";

//feito com bootblocks.com.br
  $("#card_add_saldo").css("background-color", "#68C918");
  $("#card_add_saldo").css("display", "flex");
  $("#card_add_saldo").css("align-items", "center");
  $("#card_add_saldo").css("display", "flex");
  $("#card_add_saldo").css("justify-content", "center");
  $("#txt_add_saldo").css("display", "flex");
  $("#txt_add_saldo").css("align-items", "center");
  $("#"+'icone_add_saldo').css("padding-left", 0+ "px");
  $("#"+'icone_add_saldo').css("padding-right", 5+ "px");
  $("#"+'icone_add_saldo').css("padding-top", 0+ "px");
  $("#"+'icone_add_saldo').css("padding-bottom", 0+ "px");
  $("#"+'icone_txt_saldo').css("padding-left", 0+ "px");
  $("#"+'icone_txt_saldo').css("padding-right", 5+ "px");
  $("#"+'icone_txt_saldo').css("padding-top", 0+ "px");
  $("#"+'icone_txt_saldo').css("padding-bottom", 0+ "px");
  $("#txt_total").css("display", "flex");
  $("#txt_total").css("align-items", "center");

$(document).on("click", "#card_add_saldo", function(){
  $("#add_saldo").modal("show");
});

//feito com bootblocks.com.br
  $("#card_saldo").css("background-color", "#efd400");
  $("#tela_cabecalho").css("background-color", "#169FA3");
  $("#tela_cabecalho").css("display", "flex");
  $("#tela_cabecalho").css("justify-content", "center");
  $("#tela_cabecalho").css("display", "flex");
  $("#tela_cabecalho").css("align-items", "center");
  $("#div_valor").css("display", "flex");
  $("#div_valor").css("align-items", "center");

//feito com bootblocks.com.br
  link_pagamento = '';
  largura_da_tela = (window.innerWidth * (100 / 100));
  nome_cliente = localStorage.getItem('nome_cliente') || '';
  nome_cliente = nome_cliente.split(' ')[0];
  latitude_usuario = localStorage.getItem('latitude') || '-21.6680125';
  longitude_usuario = localStorage.getItem('longitude') || '-42.4127364';
  token = localStorage.getItem('token') || '';
  url_principal = localStorage.getItem('url_principal') || '';
  cidade_id = localStorage.getItem('cidade_id') || '';
  cliente_id = localStorage.getItem('cliente_id') || '';
  senha = localStorage.getItem('senha_cliente') || '';
  token_mp = localStorage.getItem('token_mp') || '';
  url_do_app = localStorage.getItem('url_do_app') || '';
  telefone = localStorage.getItem('telefone_cliente') || '';
  ajax_post_async((String(url_principal) + 'get_dados_carteira.php'), {telefone:telefone, senha:senha}, preencher_saldo);

$(document).on("click", "#atualizar_saldo", function(){
  $("#"+'loading').show();
  ajax_post_async((String(url_principal) + 'verifica_status_transacoes.php'), {cidade_id:cidade_id, user_id:cliente_id}, finalizar_atualizacao);
});

$(document).on("click", "#btn_pagar_fatura", function(){
  window.location.href = link_fatura;
});
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