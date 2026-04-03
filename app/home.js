let avancar_1 = false;
const MAPBOX_ACCESS_TOKEN = "pk.eyJ1IjoidWJlemFwLW1vYmlsaWRhZGUtdXJiYW5hIiwiYSI6ImNtaGYxM3d3NDAwZ2gycnNjeG9qczU4bmoifQ.2VcBJf-QkhB2Ky9Sm8_B7w";
// Incluido verificação de destino por sugestão
document.addEventListener('DOMContentLoaded', function () {
  const btnAvancar = document.getElementById('btn_avancar');

  if (btnAvancar) {
    btnAvancar.addEventListener('click', function () {
      const origem = document.getElementById('box_origem').value;
      const destino = document.getElementById('box_destino').value;
      const lblAvancar = document.getElementById('lbl_avancar');

      // Valida se o destino foi selecionado corretamente
      if (!endereco_final) {
        Swal.fire('Selecione um destino na lista de sugestões'); 
        return;
      }

      if (origem && destino) {
        // Desabilita botão e altera aparência
        btnAvancar.classList.add('disabled');
        btnAvancar.style.pointerEvents = 'none';
        btnAvancar.style.opacity = '0.6';

        lblAvancar.textContent = "Aguarde...";
        $("#loading").show();

        // Chamada AJAX para calcular os custos
        ajax_post_async(
          String(url_principal) + 'calcular_custos.php',
          {
            cidade_id: cidade_id,
            lat_ini: latitude_inicial,
            lng_ini: lonngitude_inicial,
            lat_fim: latitude_final,
            lng_fim: longitude_final
          },
          exibir_categorias
        );
      } else {
        Swal.fire('Preencha origem e destino!');
      }
    });
  }
});

var dados, latitude, longitude, velocidade, altitude, resposta, motoristas, dados_inicio, retorno_categorias, index, resposta_saldo, resultado_retorno, dados_status, menu_aberto, dados_cidade, latitude_usuario, endereco_inicial, endereco_texto, iniciar_listagem, id_categori_escolhida, aceirou_aviso, forma_pagamento, tamanho_msg, largura_menu, largura_da_tela, longitude_usuario, endereco_final, endereco, motorista_id, contador, valor_corrida, tempo_timer, status_minimizado, categorias_minimizado, altura_tela_categorias, temp_aviso_motorista_chegou, latitude_inicial, lat_motorista, latitude_final, url_principal, forma_de_pagamento, minutos, temporizador_busca_motoristas, altura_tela_status, status_anterior, nome_cliente, lonngitude_inicial, senha, lng_motorista, longitude_final, telefone, lista_de_categorias, temporizador_relogio, status_texto, lat_resposta_1, long_resposta_2, dados_viagem, msg, cidade_id, Item, polilinha_um, km, lat_motorista_selecionado, cliente_id, token, temporizador_busca_status, lng_motorista_selecionado, lat_corrida_ini, lng_corrida_ini, url_imagem, lng_corrida_fim, lat_corrida_fim, lat_motorista_numero, lng_motorista_numero, polilinha;
$("#btn_avancar_escolha").css("background-color", "#009900");
// Descreva esta função...
function fechar_menu() {
  menu_aberto = false;
  largura_menu = largura_da_tela - largura_da_tela * 2;
  $("#tela_menu").css("left", largura_menu + "px");
  $("#tela_menu").css("top", "0px");
  $("#tela_menu").css("z-index", "25");
  $("#tela_menu").css("position", "fixed");
  $("#tela_menu").css("display", "block");
  console.log(largura_da_tela);
}

function getBanners(cidade_id, url_imagem) {
  console.log("Carregando banners para a cidade com ID: " + cidade_id);
  url_banners = url_imagem;
  $.ajax({
    url: String(url_principal) + 'get_banners.php',
    type: "POST",
    data: { cidade_id: cidade_id },
    success: function (resposta) {
      resposta = JSON.parse(resposta);
      if (Array.isArray(resposta) && resposta.length > 0) {
        let bannersHtml = '';
        resposta.forEach(function (banner, idx) {
          bannersHtml += `
          <div class="banner-item" style="display:${idx === 0 ? 'inline-block' : 'none'}; margin:5px;" data-banner-idx="${idx}">
            <a href="${banner.link}" target="_blank">
          <img src="${url_banners}${banner.img}" alt="banner" style="max-width:100%; border-radius:8px;">
            </a>
          </div>
        `;
        });
        $("#banners_rotativos").html(bannersHtml);
        $("#banners_rotativos").show();

        // Banner rotation logic
        if (resposta.length > 1) {
          let currentIdx = 0;
          // Clear any previous interval to avoid multiple timers
          if (window.bannerRotationInterval) clearInterval(window.bannerRotationInterval);
          window.bannerRotationInterval = setInterval(function () {
            let banners = $("#banners_rotativos .banner-item");
            banners.hide();
            currentIdx = (currentIdx + 1) % banners.length;
            $(banners[currentIdx]).show();
          }, 4000);
        }
      } else {
        $("#banners_rotativos").html('<div style="padding:10px;">Nenhum banner disponível.</div>');
      }
    },
    error: function () {
      $("#banners_rotativos").html('<div style="padding:10px;">Erro ao carregar banners.</div>');
    }
  });
}

function decodePolyline(str) {
  let index = 0, lat = 0, lng = 0, coordinates = [], shift, result, byte;

  while (index < str.length) {
    shift = result = 0;
    do {
      byte = str.charCodeAt(index++) - 63;
      result |= (byte & 0x1F) << shift;
      shift += 5;
    } while (byte >= 0x20);
    lat += (result & 1) ? ~(result >> 1) : (result >> 1);

    shift = result = 0;
    do {
      byte = str.charCodeAt(index++) - 63;
      result |= (byte & 0x1F) << shift;
      shift += 5;
    } while (byte >= 0x20);
    lng += (result & 1) ? ~(result >> 1) : (result >> 1);

    coordinates.push([lng / 1e5, lat / 1e5]);
  }
  return coordinates;
}


// Cache para rotas já buscadas com controle de tamanho
const polylineCache = {};
const MAX_CACHE_SIZE = 100; // Limite de itens no cache

function getPolylineMapbox(latitude_inicial, lonngitude_inicial, latitude_final, longitude_final, waypoints = []) {
  // Arredonda coordenadas para evitar chaves muito específicas (precisão de ~11m)
  const lat1 = parseFloat(latitude_inicial).toFixed(4);
  const lng1 = parseFloat(lonngitude_inicial).toFixed(4);
  const lat2 = parseFloat(latitude_final).toFixed(4);
  const lng2 = parseFloat(longitude_final).toFixed(4);

  // Monta chave do cache incluindo os waypoints (se houver)
  const wpKey = waypoints.map(wp => `${parseFloat(wp.lat).toFixed(4)},${parseFloat(wp.lng).toFixed(4)}`).join("|");
  const cacheKey = `${lat1},${lng1}|${wpKey}|${lat2},${lng2}`;

  // Se já existe no cache, usa o resultado salvo
  if (polylineCache[cacheKey]) {
    const data = polylineCache[cacheKey];
    renderPolylineFromData(data, true); // true indica que veio do cache
    return;
  }

  // Monta lista de coordenadas para a requisição
  let coordinates = `${lonngitude_inicial},${latitude_inicial}`;
  if (waypoints.length > 0) {
    const wpCoords = waypoints.map(wp => `${wp.lng},${wp.lat}`).join(";");
    coordinates += ";" + wpCoords;
  }
  coordinates += `;${longitude_final},${latitude_final}`;

  const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${coordinates}?geometries=geojson&access_token=${MAPBOX_ACCESS_TOKEN}`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (!data.routes || data.routes.length === 0) {
        alert("Erro ao obter rota do Mapbox.");
        return;
      }

      // Controla o tamanho do cache
      if (Object.keys(polylineCache).length >= MAX_CACHE_SIZE) {
        // Remove o primeiro item (FIFO - First In, First Out)
        const firstKey = Object.keys(polylineCache)[0];
        delete polylineCache[firstKey];
      }

      // Salva no cache
      polylineCache[cacheKey] = data;
      renderPolylineFromData(data, false); // false indica que é uma nova requisição
    })
    .catch(error => {
      console.error("Erro ao buscar a rota do Mapbox:", error);
      alert("Erro ao buscar a rota.");
    });
}


// Função auxiliar para desenhar a polilinha e atualizar tempo/distância
function renderPolylineFromData(data, fromCache = false) {
  const coordinates = data.routes[0].geometry.coordinates;
  const tempo = Math.round(data.routes[0].duration / 60); // Tempo em minutos
  const distancia = (data.routes[0].distance / 1000).toFixed(2); // Distância em km
  tempo_chegada = tempo;
  km_chegada = distancia;

  const path = coordinates.map(coord => new google.maps.LatLng(coord[1], coord[0]));

  const polyline = new google.maps.Polyline({
    path: path,
    geodesic: true,
    strokeColor: "#545454",
    strokeOpacity: 1.0,
    strokeWeight: 4,
    map: map
  });

  Polylines.push(polyline);

  const bounds = new google.maps.LatLngBounds();
  path.forEach(latLng => bounds.extend(latLng));
  map.fitBounds(bounds);

  console.log(`Rota do Mapbox obtida com sucesso ${fromCache ? '(do cache)' : '(nova requisição)'}.`);
}


// Descreva esta função...
function gerar_dados_cidade(dados) {
  dados_cidade = JSON.parse(dados);
  if (localStorage.getItem('latitude') || '') {
    latitude_usuario = localStorage.getItem('latitude') || '';
  } else {
    latitude_usuario = dados_cidade['latitude'];
  }
  if (localStorage.getItem('longitude') || '') {
    longitude_usuario = localStorage.getItem('longitude') || '';
  } else {
    longitude_usuario = dados_cidade['longitude'];
  }
  localStorage.setItem('token_mp', dados_cidade['token']);
  $("#txt_contato").html((['Telefone: ', dados_cidade['telefone'], '<br> Email: ', dados_cidade['email']].join('')));
  $("#txt_nome_telefone_dados").html((['Nome: ', localStorage.getItem('nome_cliente') || '', '<br> Telefone: ', localStorage.getItem('telefone_cliente') || ''].join('')));
  map.panTo(new google.maps.LatLng((txt_to_number(latitude_usuario)), (txt_to_number(longitude_usuario))));
}

// Descreva esta função...
function abrir_menu() {
  menu_aberto = true;
  $("#" + 'tela_menu').show();
  $("#" + 'tela_menu').css("position", "relative");
  $("#" + 'tela_menu').animate({ left: 0 + "px" }, 300);
}

// Descreva esta função...
function alterar_senha() {
  if (!document.getElementById('dados_senha_1').value.length || !document.getElementById('dados_senha_2').value.length) {
    Swal.fire({
      icon: 'error',
      title: 'Campo vazio',
      text: 'Preencha os campos de senha'
    });
  } else {
    if (document.getElementById('dados_senha_1').value == document.getElementById('dados_senha_2').value) {
      if (!localStorage.getItem('senha_cliente') || ''.length) {
        Swal.fire({
          icon: 'error',
          title: 'Não Logado',
          text: 'Faça login novamente'
        });
      } else {
        ajax_post_async((String(url_principal) + 'redefinir_senha_logado.php'), { cliente_id: cliente_id, nova_senha: document.getElementById('dados_senha_1').value, senha_atual: senha, token: token }, finaliza_redefinir_senha);
      }
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Senha inválida',
        text: 'Senhas não conferem'
      });
    }
  }
}

// Descreva esta função...
function verificar_saudacao() {
  if ((new Date().getHours()) >= 0) {
    $("#lbl_boas_vindas").html(('Bom dia, ' + String(nome_cliente)));
  }
  if ((new Date().getHours()) >= 12) {
    $("#lbl_boas_vindas").html(('Boa tarde, ' + String(nome_cliente)));
  }
  if ((new Date().getHours()) >= 18) {
    $("#lbl_boas_vindas").html(('Boa noite, ' + String(nome_cliente)));
  }
}

// Descreva esta função...
function fechar_modal() {
  $("#modal_contato").modal("hide");
}

// Descreva esta função...
function enviar_whats_contato() {
  let msg_uri_encoded = window.encodeURIComponent('Olá');
  window.open("https://api.whatsapp.com/send?phone=" + ('+55' + String(dados_cidade['telefone'])) + "&text=" + msg_uri_encoded, "_blank");
}

// Descreva esta função...
function fechar_modal_dados() {
  $("#modal_dados").modal("hide");
}

// Descreva esta função...
function finaliza_redefinir_senha(resposta) {
  Swal.fire(JSON.parse(resposta)['mensagem']);
  $("#modal_dados").modal("hide");
  if (JSON.parse(resposta)['status'] == 'sucesso') {
    senha = document.getElementById('dados_senha_1').value;
    localStorage.setItem('senha_cliente', senha);
  }
  $("#dados_senha_1").val('');
  $("#dados_senha_2").val('');
}

// Descreva esta função...
function obter_endereco_usuario() {
  if (latitude_usuario && longitude_usuario) {
    function geocodeLatLng() {
      var geocoder = new google.maps.Geocoder();
      var latlng = { lat: (txt_to_number(latitude_usuario)), lng: (txt_to_number(longitude_usuario)) };
      geocoder.geocode({ 'location': latlng }, function (results, status) {
        if (status === 'OK') {
          if (results[0]) {
            endereco = results[0].formatted_address;
            latitude_inicial = latitude_usuario;
            lonngitude_inicial = longitude_usuario;
            $("#box_origem").val(endereco);
          } else {
            window.alert('Nenhum Resultado Encontrado');
          }
        } else {
          window.alert('Geocoder falhou: ' + status);
        }
      });
    }
    geocodeLatLng();
  }
}

function mathRandomInt(a, b) {
  if (a > b) {
    // Swap a and b to ensure a is smaller.
    var c = a;
    a = b;
    b = c;
  }
  return Math.floor(Math.random() * (b - a + 1) + a);
}

// Descreva esta função...
function mostrar_motoristas(motoristas) {
  if (motoristas) {
    motorista_id = 0;
    lat_motorista = 0;
    lng_motorista = 0;
    motoristas = JSON.parse(motoristas);
    for (var Item_index in motoristas) {
      Item = motoristas[Item_index];
      motorista_id = Item['id'];
      for (var i = 0; i < Makers.length; i++) {
        if (Makers[i].marker_id === motorista_id) {
          Makers[i].setMap(null);
          Makers.splice(i, 1);
        }
      }
      lng_motorista = Item['longitude'];
      lat_motorista = Item['latitude'];
      lng_motorista = (txt_to_number(lng_motorista));
      lat_motorista = (txt_to_number(lat_motorista));
      if (Item['online'] == 1) {
        if (mathRandomInt(1, 2) % 2 === 0) {
          var marker = new google.maps.Marker({
            position: { lat: lat_motorista, lng: lng_motorista },
            map: map,
            icon: "assets/car_green_a.png",
            title: "Motorista Disponível",
            marker_id: motorista_id
          });
          marker.addListener("click", function () {
            let id = this.marker_id;
          });
          Makers.push(marker);
        } else {
          var marker = new google.maps.Marker({
            position: { lat: lat_motorista, lng: lng_motorista },
            map: map,
            icon: "assets/car_green_b.png",
            title: "Motorista Disponível",
            marker_id: motorista_id
          });
          marker.addListener("click", function () {
            let id = this.marker_id;
          });
          Makers.push(marker);
        }
      } else {
        if (mathRandomInt(1, 2) % 2 === 0) {
          var marker = new google.maps.Marker({
            position: { lat: lat_motorista, lng: lng_motorista },
            map: map,
            icon: "assets/car_red_a.png",
            title: "Motorista Ocupado",
            marker_id: motorista_id
          });
          marker.addListener("click", function () {
            let id = this.marker_id;
          });
          Makers.push(marker);
        } else {
          var marker = new google.maps.Marker({
            position: { lat: lat_motorista, lng: lng_motorista },
            map: map,
            icon: "assets/car_red_b.png",
            title: "Motorista Ocupado",
            marker_id: motorista_id
          });
          marker.addListener("click", function () {
            let id = this.marker_id;
          });
          Makers.push(marker);
        }
      }
    }
  }
}

// Descreva esta função...
function proximo_input() {
  function addAutocomplete() {
    var input = document.getElementById('box_destino');
    let radius = 50000;
    let center = new google.maps.LatLng(latitude_usuario, longitude_usuario);
    let circle = new google.maps.Circle({
      center: center,
      radius: radius
    });
    let options = {
      bounds: circle.getBounds()
    };
    autocomplete_box_destino = new google.maps.places.Autocomplete(input, options);
    autocomplete_box_destino.addListener("place_changed", () => {
      let place = autocomplete_box_destino.getPlace();
      
      // Validação: verificar se o lugar tem geometria (coordenadas)
  if (!place.geometry) {
    Swal.fire('Erro: ZERO_RESULTS', 'Não foi possível encontrar as coordenadas deste local. Tente um endereço mais específico.', 'error');
    document.getElementById('box_destino').value = '';
    return;
  }
  
  // Validação: verificar se não é apenas um país
  if (place.types && place.types.includes('country')) {
    Swal.fire('Endereço muito amplo', 'Por favor, selecione um endereço mais específico, como uma rua, bairro ou cidade, não apenas o país.', 'warning');
    document.getElementById('box_destino').value = '';
    return;
  }
      
      endereco_texto = place.formatted_address;
      latitude = place.geometry.location.lat();
      longitude = place.geometry.location.lng();
      endereco_final = endereco_texto;
      latitude_final = latitude;
      longitude_final = longitude;
      var marker = new google.maps.Marker({
        position: { lat: latitude, lng: longitude },
        map: map,
        icon: "assets/destino.png",
        title: "Destino",
        marker_id: 2
      });
      marker.addListener("click", function () {
        let id = this.marker_id;
      });
      Makers.push(marker);
      function getPolyline() {
        directionsService = new google.maps.DirectionsService();
        let request = {
          origin: new google.maps.LatLng(latitude_inicial, lonngitude_inicial),
          destination: new google.maps.LatLng(latitude, longitude),
          travelMode: google.maps.TravelMode.DRIVING,
          unitSystem: google.maps.UnitSystem.METRIC,
          durationInTraffic: true,
        };
        directionsService.route(request, function (response, status) {
          if (status == google.maps.DirectionsStatus.OK) {
            polilinha_um = response.routes[0].overview_polyline;
            var polyline = new google.maps.Polyline({
              strokeColor: "#545454",
              strokeOpacity: 1,
              strokeWeight: 4,
              map: map
            });
            polyline.polyline_id = 9;
            polyline.setPath(google.maps.geometry.encoding.decodePath(polilinha_um));
            Polylines.push(polyline);
          } else {
            alert("Erro: " + status);
          }
        });
      }
      getPolyline();
    });
  }
  addAutocomplete();
}

// Descreva esta função...
function busca_inicio(dados_inicio) {
  if (dados_inicio) {
    ajax_post_async((String(url_principal) + 'get_status_chamado.php'), { telefone: telefone, senha: senha }, verifica_status);
    $("#" + 'tela_status').show();
    $("#" + 'tela_barra_inicio').hide();
    resultado_chamado([]);
  }
}

// Descreva esta função...
function n_cancelar() {
}

// Descreva esta função...
function cancelar() {
  ajax_post_async((String(url_principal) + 'cancelar.php'), { telefone: telefone, senha: senha }, fim_cancelar);
}

// Descreva esta função...
function fim_cancelar() {
  window.location.href = "home.php";
}

// Descreva esta função...
function exibir_categorias(retorno_categorias) {
  iniciar_listagem = true;
  contador = 0;
  id_categori_escolhida = 0;
  lista_de_categorias = JSON.parse(retorno_categorias)['categorias'];
  dados_viagem = JSON.parse(retorno_categorias)['dados'];
  km = dados_viagem['km'];
  minutos = dados_viagem['minutos'];
  // Limpa o conteúdo do div
  document.getElementById("tela_lista_categorias").innerHTML = "";
  for (var Item_index2 in lista_de_categorias) {
    Item = lista_de_categorias[Item_index2];
    contador = contador + 1;
    var card = '<div onclick="mudar_categoria(' + contador + ')" class="meus_cards" id="' + contador + '" style="width:98%; margin:2px; padding: 5px; border-radius: 5px; box-shadow: 7px 7px 13px 0px rgba(50, 50, 50, 0.22);">'
    card += '<div class="row">'
    card += '<div class="col-4">'
    card += '<img class="imagem_meus_cards" id="imagem_meus_cards" style="width:50px; height:50px;" src="' + (String(url_imagem) + String(Item['img'])) + '" alt="imagem">'
    card += '</div>'
    card += '<div class="col-8">'
    card += '<span class="titulo_meus_cards" id="titulo_meus_cards" style="font-weight: bold; font-size: 16px">' + (['<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + Item['nome'] + ' </span>', '&nbsp', String('<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + 'R$ ' + ' </span>') + String('<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + Item['taxa'] + ' </span>')].join('')) + '</span><br>'
    card += '<span class="subtitulo_meus_cards" id="subtitulo_meus_cards" style="font-size: 13px">' + '<span style="font-size:14px; color:#000000; font-weight:normal; font-style:normal;">' + Item['descricao'] + ' </span>' + '</span><br>'
    // if (Item['motorista_km'] != 0 && Item['motorista_tempo'] != 0) {
    //   card += '<span class="info_motorista_categoria" style="font-size:13px; font-weight:bold;">A ' + Item['motorista_km'] + ' Km e ' + Item['motorista_tempo'] + ' Min</span><br>';
    // }
    card += '<span class="texto_adicional_meus_cards" id="texto_adicional_meus_cards" style="font-size: 13px">' + '<span style="font-size:12px; color:#666666; font-weight:normal; font-style:normal;">' + 'Clique para selecionar' + ' </span>' + '</span>'
    card += '</div>'
    card += '</div>'
    card += ' </div>'
    document.getElementById("tela_lista_categorias").innerHTML += card;
    if (iniciar_listagem) {
      iniciar_listagem = false;
      id_categori_escolhida = Item['id'];
      valor_corrida = Item['taxa'];
      $("#txt_card_chamado").html((['Confirmar ', Item['nome'], ' R$ ', Item['taxa']].join('')));
      document.getElementById(contador).style.border = 1 + "px solid " + "#009900";
    } else {
      document.getElementById(contador).style.border = 1 + "px solid " + "#c0c0c0";
    }
  }
  $("." + 'meus_cards').css("margin-left", 2 + "px");
  $("." + 'meus_cards').css("margin-right", 2 + "px");
  $("." + 'meus_cards').css("margin-top", 8 + "px");
  $("." + 'meus_cards').css("margin-bottom", 8 + "px");
  $("." + 'imagem_meus_cards').css("padding-left", 20 + "px");
  $("." + 'imagem_meus_cards').css("padding-right", 0 + "px");
  $("." + 'imagem_meus_cards').css("padding-top", 5 + "px");
  $("." + 'imagem_meus_cards').css("padding-bottom", 0 + "px");
  $(".imagem_meus_cards").css("height", '80' + "px");
  $(".imagem_meus_cards").css("width", '80' + "px");
  $("#" + 'tela_destinos').hide();
  $("#" + 'tela_barra_inicio').hide();
  $("#" + 'tela_btn_avancar').hide();
  document.getElementById('tela_categorias').style.position = "fixed";
  document.getElementById('tela_categorias').style.bottom = "0px";
  document.getElementById('tela_categorias').style.left = "0";
  document.getElementById('tela_categorias').style.right = "0";
  document.getElementById('tela_categorias').style.zIndex = "20";
  $("#" + 'tela_categorias').show();
  console.log(retorno_categorias);
}
var podeAlterarCategoria = true;
// Descreva esta função...
function mudar_categoria(index) {
  if (!podeAlterarCategoria) {
    swal.fire('Não é possível alterar a categoria após a aplicação do cupom.');
    return;
  }
  id_categori_escolhida = (lista_de_categorias[(index - 1)])['id'];
  valor_corrida = (lista_de_categorias[(index - 1)])['taxa'];
  $("#txt_card_chamado").html((['Confirmar ', (lista_de_categorias[(index - 1)])['nome'], ' R$ ', (lista_de_categorias[(index - 1)])['taxa']].join('')));
  $(".meus_cards").css("border", 1 + "px solid #cccccc");
  document.getElementById(index).style.border = 1 + "px solid " + "#009900";
}

// Descreva esta função...
function enviar_solicitacao_chamado() {
  if (!endereco_inicial) {
    endereco_inicial = document.getElementById('box_origem').value;
  }
  var obs = document.getElementById('box_obs') ? document.getElementById('box_obs').value : '';
  let codigo_cupom = document.getElementById('cupom_desconto_input') ? document.getElementById('cupom_desconto_input').value : '';
  $("#" + 'tela_categorias').hide();
  forma_de_pagamento = $("input[name=forma_pagamento]:checked").val();
  $("#" + 'loading').show();
  ajax_post_async((String(url_principal) + 'insere_chamado.php'),
    {
      senha: senha,
      telefone: telefone,
      valor: valor_corrida,
      forma_pagamento: forma_pagamento,
      endereco_ini: endereco_inicial,
      endereco_fim: endereco_final,
      categoria_id: id_categori_escolhida,
      lat_ini: latitude_inicial,
      lng_ini: lonngitude_inicial,
      lat_fim: latitude_final,
      lng_fim: longitude_final,
      km: km,
      tempo: minutos,
      taxa: valor_corrida,
      obs: obs,
      cupom: codigo_cupom
    }, resultado_chamado);
}

// Descreva esta função...
function verifica_saldo(resposta_saldo) {
  resposta_saldo = JSON.parse(resposta_saldo);
  if (resposta_saldo['status'] == 'erro') {
    Swal.fire('Saldo insuficiente na carteira de crédito! Escolha outra forma de pagamento');
  } else {
    enviar_solicitacao_chamado();
  }
}

// Descreva esta função...
function resultado_chamado(resultado_retorno) {
  $("#" + 'loading').hide();
  tempo_timer = 0;
  minutos = 0;
  temporizador_relogio = setInterval(function () {
    tempo_timer = tempo_timer + 1;
    if (tempo_timer > 59) {
      tempo_timer = 0;
      minutos = minutos + 1;
    }
    if (tempo_timer < 10) {
      $("#txt_timer").html(([minutos, ':', '0', tempo_timer].join('')));
    } else {
      $("#txt_timer").html(([minutos, ':', tempo_timer].join('')));
    }
  }, 1000);
  $("#" + 'tela_status').show();
  document.getElementById('tela_status').style.position = "fixed";
  document.getElementById('tela_status').style.bottom = "0px";
  document.getElementById('tela_status').style.left = "0";
  document.getElementById('tela_status').style.right = "0";
  document.getElementById('tela_status').style.zIndex = "29";
  temporizador_busca_status = setInterval(function () {
    ajax_post_async((String(url_principal) + 'get_status_chamado.php'), { telefone: telefone, senha: senha }, verifica_status);
  }, 5000);
  clearInterval(temporizador_busca_motoristas);
  $("#" + 'reprodutor_lottie_1').show();
  $("#" + 'tela_timer').show();
  deletar_itens_mapa();
}

// Descreva esta função...
function deletar_itens_mapa() {
  for (var i = 0; i < Makers.length; i++) {
    Makers[i].setMap(null);
  }
  Makers = [];
  for (var i = 0; i < Polylines.length; i++) {
    Polylines[i].setMap(null);
  }
  Polylines = [];
}

// Descreva esta função...
function verifica_status(dados_status) {
  console.log(dados_status);
  deletar_itens_mapa();
  clearInterval(temporizador_busca_motoristas);
  dados_status = JSON.parse(dados_status);
  status_texto = dados_status['status_string'];
  msg = dados_status['msg'];
  lat_motorista_selecionado = (txt_to_number(dados_status['latitude']));
  lng_motorista_selecionado = (txt_to_number(dados_status['longitude']));
  lat_corrida_ini = (txt_to_number(dados_status['lat_ini']));
  lng_corrida_ini = (txt_to_number(dados_status['lng_ini']));
  lng_corrida_fim = (txt_to_number(dados_status['lng_fim']));
  lat_corrida_fim = (txt_to_number(dados_status['lat_fim']));
  $('#tela_info_status').css('display', 'none');
  if (msg) {
    if (msg.length > tamanho_msg) {
      tamanho_msg = msg.length;
      $("#icone_chat").html('mark_unread_chat_alt');
      $("#icone_chat").css("color", "#ff6600");
      $("#icone_chat").css("font-size", "28px");
      $("#icone_chat").css("font-style", "normal");
      $("#icone_chat").css("font-weight", "normal");
      function rotateElement(element, angle) {
        let el = document.getElementById(element);
        el.style.transition = "transform " + 1000 + "ms";
        el.style.transform = "rotate(" + angle + "deg)";
      }
      rotateElement('icone_chat', 360);
      document.getElementById('audio_message').play();
    }
  }
  if (status_texto != status_anterior) {
    document.getElementById('audio').play();
    status_anterior = status_texto;
  }
  if (status_texto == 'Procurando Motorista') {
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'tela_img_motorista').hide();
    $("#" + 'tela_dados_motorista').hide();
    $("#" + 'reprodutor_lottie_1').show();
    $("#" + 'tela_timer').show();
    ajax_post_async((String(url_principal) + 'get_all_motoristas.php'), { senha: senha, telefone: telefone }, mostrar_motoristas);
  }
  if (status_texto == 'Motorista a Caminho') {
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'reprodutor_lottie_1').hide();
    $("#" + 'tela_timer').hide();
    $("#" + 'img_motorista').attr("src", (String(url_imagem) + String(dados_status['motorista_img'])));
    $("#" + 'tela_img_motorista').show();
    $("#dados_motorista").html((['<span style="font-size:18px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['motorista_nome'] + ' </span>', '<br>', '<span style="font-size:15px; color:#000000; font-weight:normal; font-style:normal;">' + (dados_status['avaliacao'] > 0 && dados_status['avaliacao'] < 1 ? '⭐' : (dados_status['avaliacao'] > 1 && dados_status['avaliacao'] < 2 ? '⭐⭐' : (dados_status['avaliacao'] > 2 && dados_status['avaliacao'] < 3 ? '⭐⭐⭐' : (dados_status['avaliacao'] > 3 && dados_status['avaliacao'] < 4 ? '⭐⭐⭐⭐' : (dados_status['avaliacao'] > 4 && dados_status['avaliacao'] < 5 ? '⭐⭐⭐⭐⭐' : '⭐⭐⭐⭐⭐'))))) + ' </span>', '', '', '<br>', '<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['veiculo'] + ' </span>', '<br>', '<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['placa'] + ' </span>'].join('')));
    var marker = new google.maps.Marker({
      position: { lat: lat_motorista_selecionado, lng: lng_motorista_selecionado },
      map: map,
      icon: "assets/car_green_a.png",
      title: "dados_status[motorista]",
      marker_id: 3
    });
    marker.addListener("click", function () {
      let id = this.marker_id;
    });
    Makers.push(marker);
    $("#dados_motorista").css("text-align", "center");
    $("#" + 'tela_dados_motorista').show();
    if (lat_motorista_selecionado && lng_motorista_selecionado) {
      if (lat_corrida_ini && lng_corrida_ini) {
        lat_motorista_numero = (txt_to_number(lat_motorista_selecionado));
        lng_motorista_numero = (txt_to_number(lng_motorista_selecionado));
        getPolylineMapbox(lat_motorista_numero, lng_motorista_numero, lat_corrida_ini, lng_corrida_ini, []);
        var marker = new google.maps.Marker({
          position: { lat: lat_corrida_ini, lng: lng_corrida_ini },
          map: map,
          icon: "assets/marcador.png",
          title: "Origem",
          marker_id: 10
        });
        marker.addListener("click", function () {
          let id = this.marker_id;
        });
        Makers.push(marker);
      } else {
        console.log((['Lat,Lng partida: ', latitude_inicial, lonngitude_inicial].join('')));
      }
    } else {
      console.log((['Lat,Lng motorista: ', lat_motorista_selecionado, lng_motorista_selecionado].join('')));
    }
    //mostra tempo de chegada
    $('#tela_info_status').css('display', 'flex');
    $('#txt_info_status').html('Chegará em: ' + dados_status['tempo_motorista'] + ' minutos');
    console.log(dados_status['tempo_motorista']);
  }
  if (status_texto == 'Motorista Chegou') {
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'reprodutor_lottie_1').hide();
    $("#" + 'tela_timer').hide();
    $("#" + 'img_motorista').attr("src", (String(url_imagem) + String(dados_status['motorista_img'])));
    $("#" + 'tela_img_motorista').show();
    $("#dados_motorista").html((['<span style="font-size:18px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['motorista_nome'] + ' </span>', '<br>', '<span style="font-size:15px; color:#000000; font-weight:normal; font-style:normal;">' + (dados_status['avaliacao'] > 0 && dados_status['avaliacao'] < 1 ? '⭐' : (dados_status['avaliacao'] > 1 && dados_status['avaliacao'] < 2 ? '⭐⭐' : (dados_status['avaliacao'] > 2 && dados_status['avaliacao'] < 3 ? '⭐⭐⭐' : (dados_status['avaliacao'] > 3 && dados_status['avaliacao'] < 4 ? '⭐⭐⭐⭐' : (dados_status['avaliacao'] > 4 && dados_status['avaliacao'] < 5 ? '⭐⭐⭐⭐⭐' : '⭐⭐⭐⭐⭐'))))) + ' </span>', '<br>', '<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['veiculo'] + ' </span>', '<br>', '<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['placa'] + ' </span>'].join('')));
    $("#dados_motorista").css("text-align", "center");
    $("#" + 'tela_dados_motorista').show();
    var marker = new google.maps.Marker({
      position: { lat: lat_motorista_selecionado, lng: lng_motorista_selecionado },
      map: map,
      icon: "assets/car_green_a.png",
      title: "dados_status[motorista]",
      marker_id: 3
    });
    marker.addListener("click", function () {
      let id = this.marker_id;
    });
    Makers.push(marker);
    if (!aceirou_aviso) {
      if (!temp_aviso_motorista_chegou) {
        temp_aviso_motorista_chegou = setInterval(function () {
          document.getElementById('audio').play();
        }, 5000);
        Swal.fire({
          title: 'Motorista Chegou ao ponto de embarque!',
          showCancelButton: false,
          confirmButtonText: 'OK',
          cancelButtonText: '',
        }).then((result) => {
          if (result.value) {
            remover_aviso()
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            rec()
          }
        });
      }
    }
  }
  if (status_texto == 'Em Viagem') {
    if (lat_motorista_selecionado && lng_motorista_selecionado) {
      if (lat_corrida_fim && lng_corrida_fim) {
        lat_motorista_numero = (txt_to_number(lat_motorista_selecionado));
        lng_motorista_numero = (txt_to_number(lng_motorista_selecionado));
        function getPolyline() {
          directionsService = new google.maps.DirectionsService();
          let request = {
            origin: new google.maps.LatLng(lat_motorista_numero, lng_motorista_numero),
            destination: new google.maps.LatLng(lat_corrida_fim, lng_corrida_fim),
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC,
            durationInTraffic: true,
          };
          directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
              polilinha = response.routes[0].overview_polyline;
              for (var i = 0; i < Polylines.length; i++) {
                Polylines[i].setMap(null);
              }
              Polylines = [];
              var polyline = new google.maps.Polyline({
                strokeColor: "#000",
                strokeOpacity: 1,
                strokeWeight: 1,
                map: map
              });
              polyline.polyline_id = 3;
              polyline.setPath(google.maps.geometry.encoding.decodePath(polilinha));
              Polylines.push(polyline);
            } else {
              alert("Erro: " + status);
            }
          });
        }
        getPolyline();
        var marker = new google.maps.Marker({
          position: { lat: lat_corrida_fim, lng: lng_corrida_fim },
          map: map,
          icon: "assets/destino.png",
          title: "Destino",
          marker_id: 20
        });
        marker.addListener("click", function () {
          let id = this.marker_id;
        });
        Makers.push(marker);
      } else {
        console.log((['Lat,Lng partida: ', latitude_inicial, lonngitude_inicial].join('')));
      }
    } else {
      console.log((['Lat,Lng motorista: ', lat_motorista_selecionado, lng_motorista_selecionado].join('')));
    }
    $("#" + 'reprodutor_lottie_1').hide();
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'tela_img_motorista').hide();
    $("#" + 'tela_dados_motorista').hide();
    $("#" + 'reprodutor_lottie_2').show();
    $("#" + 'tela_timer').hide();
    $("#" + 'card_cancelar').hide();
    var marker = new google.maps.Marker({
      position: { lat: lat_motorista_selecionado, lng: lng_motorista_selecionado },
      map: map,
      icon: "assets/car_green_a.png",
      title: "dados_status[motorista]",
      marker_id: 3
    });
    marker.addListener("click", function () {
      let id = this.marker_id;
    });
    Makers.push(marker);
  }
  if (status_texto == 'Finalizada') {
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'tela_img_motorista').hide();
    $("#" + 'tela_dados_motorista').hide();
    $("#" + 'reprodutor_lottie_1').hide();
    $("#" + 'reprodutor_lottie_2').hide();
    $("#" + 'reprodutor_lottie_3').show();
    $("#" + 'tela_timer').hide();
    $("#" + 'card_cancelar').hide();
    $("#" + 'card_finalizar').show();
    $("#" + 'tela_txt_finalizar').show();
    $("#txt_total_fim").html((String('<span style="font-size:16px; color:#333333; font-weight:bold; font-style:normal;">' + 'Total R$ ' + ' </span>') + String('<span style="font-size:16px; color:#000000; font-weight:bold; font-style:normal;">' + dados_status['taxa'] + ' </span>')));
    clearInterval(temporizador_busca_status);
  }
  if (status_texto == 'Cancelada') {
    $("#" + 'tela_barra_inicio').hide();
    $("#" + 'tela_img_motorista').hide();
    $("#" + 'tela_dados_motorista').hide();
    $("#" + 'reprodutor_lottie_1').hide();
    $("#" + 'reprodutor_lottie_2').hide();
    $("#" + 'reprodutor_lottie_3').hide();
    $("#" + 'reprodutor_lottie_4').show();
    $("#" + 'tela_timer').hide();
    $("#" + 'card_finalizar').hide();
    $("#" + 'card_cancelar').hide();
    $("#" + 'tentar_novamente').show();
    clearInterval(temporizador_busca_status);
  }
  $("#txt_status").html(status_texto);
}

// Descreva esta função...
function remover_aviso() {
  clearInterval(temp_aviso_motorista_chegou);
  aceirou_aviso = true;
}

(function () {
  let elementoClick = document.getElementById('escolher_mapa_destino');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#endereco_escolhido_mapa").html('Arraste o pino para selecionar um endereço');
      selecionando_endereco = 'destino';
      $("#" + 'tela_destinos').hide();
      $("#" + 'tela_btn_avancar').hide();
      $("#" + 'tela_base_escolha').show();
      document.getElementById('tela_base_escolha').style.position = "fixed";
      document.getElementById('tela_base_escolha').style.bottom = "0px";
      document.getElementById('tela_base_escolha').style.left = "0";
      document.getElementById('tela_base_escolha').style.right = "0";
      document.getElementById('tela_base_escolha').style.zIndex = "20";
      let center_map = map.getCenter();
      let marker_pin = new google.maps.Marker({
        position: center_map,
        map: map,
        draggable: true,
        icon: 'assets/pino_80.png',
      });
      marker_pin.id = 10;
      marker_pin.addListener("dragend", function (event) {
        lat_pino = event.latLng.lat().toFixed(7);
        lng_pino = event.latLng.lng().toFixed(7);
        latitude_final = (txt_to_number(lat_pino));
        longitude_final = (txt_to_number(lng_pino));
        function geocodeLatLng() {
          var geocoder = new google.maps.Geocoder();
          var latlng = { lat: latitude_final, lng: longitude_final };
          geocoder.geocode({ 'location': latlng }, function (results, status) {
            if (status === 'OK') {
              if (results[0]) {
                endereco = results[0].formatted_address;
                endereco_final = endereco;
                $("#endereco_escolhido_mapa").html(endereco);
              } else {
                window.alert('Nenhum Resultado Encontrado');
              }
            } else {
              window.alert('Geocoder falhou: ' + status);
            }
          });
        }
        geocodeLatLng();
      });
      Makers.push(marker_pin);

    });
  }
})();

(function () {
  let elementoClick = document.getElementById('escolher_mapa_origem');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#endereco_escolhido_mapa").html('Arraste o pino para selecionar um endereço');
      selecionando_endereco = 'origem';
      $("#" + 'tela_destinos').hide();
      $("#" + 'tela_btn_avancar').hide();
      $("#" + 'tela_base_escolha').show();
      document.getElementById('tela_base_escolha').style.position = "fixed";
      document.getElementById('tela_base_escolha').style.bottom = "0px";
      document.getElementById('tela_base_escolha').style.left = "0";
      document.getElementById('tela_base_escolha').style.right = "0";
      document.getElementById('tela_base_escolha').style.zIndex = "20";
      let center_map = map.getCenter();
      let marker_pin = new google.maps.Marker({
        position: center_map,
        map: map,
        draggable: true,
        icon: 'assets/pino_80.png',
      });
      marker_pin.id = 10;
      marker_pin.addListener("dragend", function (event) {
        lat_pino = event.latLng.lat().toFixed(7);
        lng_pino = event.latLng.lng().toFixed(7);
        latitude_inicial = (txt_to_number(lat_pino));
        lonngitude_inicial = (txt_to_number(lng_pino));
        function geocodeLatLng() {
          var geocoder = new google.maps.Geocoder();
          var latlng = { lat: latitude_inicial, lng: lonngitude_inicial };
          geocoder.geocode({ 'location': latlng }, function (results, status) {
            if (status === 'OK') {
              if (results[0]) {
                endereco = results[0].formatted_address;
                endereco_inicial = endereco;
                $("#endereco_escolhido_mapa").html(endereco);
              } else {
                window.alert('Nenhum Resultado Encontrado');
              }
            } else {
              window.alert('Geocoder falhou: ' + status);
            }
          });
        }
        geocodeLatLng();
      });
      Makers.push(marker_pin);

    });
  }
})();

function getPolylinePin() {
  directionsService = new google.maps.DirectionsService();
  let request = {
    origin: new google.maps.LatLng(latitude_inicial, lonngitude_inicial),
    destination: new google.maps.LatLng(latitude_final, longitude_final),
    travelMode: google.maps.TravelMode.DRIVING,
    unitSystem: google.maps.UnitSystem.METRIC,
    durationInTraffic: true,
  };
  directionsService.route(request, function (response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      polilinha_um = response.routes[0].overview_polyline;
      var polyline = new google.maps.Polyline({
        strokeColor: "#545454",
        strokeOpacity: 1,
        strokeWeight: 4,
        map: map
      });
      polyline.polyline_id = 9;
      polyline.setPath(google.maps.geometry.encoding.decodePath(polilinha_um));
      Polylines.push(polyline);
    } else {
      alert("Erro: " + status);
    }
  });
}

(function () {
  let elementoClick = document.getElementById('btn_avancar_escolha');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      if (selecionando_endereco == 'origem') {
        $("#box_origem").val(endereco_inicial);
      } else {
        if (!latitude_inicial) {
          latitude_inicial = latitude;
          longitude_final = longitude;
        }
        var marker = new google.maps.Marker({
          position: { lat: latitude_inicial, lng: lonngitude_inicial },
          map: map,
          icon: "assets/marcador.png",
          title: "Origem",
          marker_id: 1
        });
        marker.addListener("click", function () {
          let id = this.marker_id;
        });
        Makers.push(marker);
        var marker = new google.maps.Marker({
          position: { lat: latitude_final, lng: longitude_final },
          map: map,
          icon: "assets/destino.png",
          title: "Destino",
          marker_id: 2
        });
        marker.addListener("click", function () {
          let id = this.marker_id;
        });
        Makers.push(marker);
        getPolylinePin(latitude_inicial, lonngitude_inicial, latitude_final, longitude_final);
        $("#box_destino").val(endereco_final);
      }
      $("#" + 'tela_destinos').show();
      $("#" + 'tela_btn_avancar').show();
      $("#" + 'tela_base_escolha').hide();
      console.log((['Lat,Lng partida: ', latitude_inicial, lonngitude_inicial].join('')));
      for (var i = 0; i < Makers.length; i++) {
        if (Makers[i].id == 10) {
          Makers[i].setMap(null);
          Makers.splice(i, 1);
        }
      }

    });
  }
})();



//feito com bootblocks.com.br
$("body").css("height", "100%");
$("html").css("height", "100%");
var map;
var Circles = [];
var Polylines = [];
var Polygons = [];
var Makers = [];
function initMap() {
  map = new google.maps.Map(document.getElementById('tela_mapa'), {
    center: { lat: (txt_to_number(latitude_usuario)), lng: (txt_to_number(longitude_usuario)) },
    zoom: 15
  });
  if (typeof onMapInitilize === "function") {
    onMapInitilize();
  }
  google.maps.event.addListener(map, 'click', function (event) {
    if (typeof onMapClick === "function") {
      onMapClick(event);
    }
  });
}
var script = document.createElement("script");
script.src = "https://maps.googleapis.com/maps/api/js?key=" + 'AIzaSyBkgsGUHbghtnadJBo-O5pjV_c1qjNzQlY' + "&libraries=places&callback=initMap";
script.async = true;
document.head.appendChild(script);


function qclick() {
  let elementoClick = document.getElementById('icone_menu');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      if (menu_aberto) {
        menu_aberto = false;
        largura_menu = largura_da_tela - largura_da_tela * 2;
        $("#" + 'tela_menu').css("position", "relative");
        $("#" + 'tela_menu').animate({ left: largura_menu + "px" }, 300);
        $("#icone_menu").html('menu');
        fechar_menu();
      } else {
        $("#icone_menu").html('menu_open');
        abrir_menu();
      }

    });
  }
}
qclick();


function qclick2() {
  let elementoClick = document.getElementById('tela_carteira_credito');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "carteira.php";
    });
  }
}
qclick2();


function qclick3() {
  let elementoClick = document.getElementById('tela_fale_conosco');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#modal_contato").modal("show");

    });
  }
}
qclick3();

//feito com bootblocks.com.br
document.getElementById('cabecalho').style.position = "fixed";
document.getElementById('cabecalho').style.top = "0px";
document.getElementById('cabecalho').style.left = "0";
document.getElementById('cabecalho').style.right = "0";
document.getElementById('cabecalho').style.zIndex = "26";
largura_da_tela = (window.innerWidth * (100 / 100));
fechar_menu();
nome_cliente = localStorage.getItem('nome_cliente') || '';
nome_cliente = nome_cliente.split(' ')[0];
latitude_usuario = localStorage.getItem('latitude') || '';
longitude_usuario = localStorage.getItem('longitude') || '';
token = localStorage.getItem('token') || '';
url_principal = localStorage.getItem('url_principal') || '';
url_imagem = localStorage.getItem('url_imagem') || '';
cidade_id = localStorage.getItem('cidade_id') || '';
cliente_id = localStorage.getItem('cliente_id') || '';
senha = localStorage.getItem('senha_cliente') || '';
telefone = localStorage.getItem('telefone_cliente') || '';
ajax_post_async((String(url_principal) + 'get_dados_cidade.php'), { token: token, cidade_id: cidade_id }, gerar_dados_cidade);
ajax_post_async((String(url_principal) + 'busca_inicio.php'), { senha: senha, telefone: telefone }, busca_inicio);
$("#lbl_nome_cliente_menu").html(('Olá ' + String(localStorage.getItem('nome_cliente') || '')));

getBanners(cidade_id, url_imagem);


function qclick4() {
  let elementoClick = document.getElementById('tela_meus_dados');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#modal_dados").modal("show");

    });
  }
}
qclick4();

//feito com bootblocks.com.br
$("#tela_label_cabecalho").css("background-color", "#000000");
$("#tela_icone_menu").css("background-color", "#000000");
$("#tela_icone_chat").css("background-color", "#000000");
$("#card_iniciar").css("background-color", "#000000");
$("#cabecalho").css("display", "flex");
$("#cabecalho").css("justify-content", "center");
$("#tela_label_cabecalho").css("display", "flex");
$("#tela_label_cabecalho").css("justify-content", "center");
$("#tela_label_cabecalho").css("display", "flex");
$("#tela_label_cabecalho").css("align-items", "center");
$("#tela_icone_menu").css("display", "flex");
$("#tela_icone_menu").css("justify-content", "center");
$("#tela_icone_menu").css("display", "flex");
$("#tela_icone_menu").css("align-items", "center");
$("#tela_icone_chat").css("display", "flex");
$("#tela_icone_chat").css("justify-content", "center");
$("#tela_icone_chat").css("display", "flex");
$("#tela_icone_chat").css("align-items", "center");
$("#" + 'icone_menu').css("padding-left", 5 + "px");
$("#" + 'icone_menu').css("padding-right", 0 + "px");
$("#" + 'icone_menu').css("padding-top", 5 + "px");
$("#" + 'icone_menu').css("padding-bottom", 0 + "px");
$("#" + 'icone_chat').css("padding-left", 0 + "px");
$("#" + 'icone_chat').css("padding-right", 5 + "px");
$("#" + 'icone_chat').css("padding-top", 5 + "px");
$("#" + 'icone_chat').css("padding-bottom", 0 + "px");
document.getElementById('cabecalho').style['border-bottom-right-radius'] = '15px';
document.getElementById('cabecalho').style['border-bottom-left-radius'] = '15px';
document.getElementById('tela_icone_menu').style['border-bottom-left-radius'] = '15px';
document.getElementById('tela_icone_chat').style['border-bottom-right-radius'] = '15px';
$("#tela_btn_avancar_escolha").css("display", "flex");
$("#tela_btn_avancar_escolha").css("justify-content", "center");

//feito com bootblocks.com.br
$("#" + 'container_menu').css("padding-left", 30 + "px");
$("#" + 'container_menu').css("padding-right", 0 + "px");
$("#" + 'container_menu').css("padding-top", 0 + "px");
$("#" + 'container_menu').css("padding-bottom", 0 + "px");
$("#tela_carteira_credito").css("display", "flex");
$("#tela_carteira_credito").css("align-items", "center");
$("#tela_meus_dados").css("display", "flex");
$("#tela_meus_dados").css("align-items", "center");
$("#tela_historico_corridas").css("display", "flex");
$("#tela_historico_corridas").css("align-items", "center");
$("#tela_fale_conosco").css("display", "flex");
$("#tela_fale_conosco").css("align-items", "center");
$("#tela_sair").css("display", "flex");
$("#tela_sair").css("align-items", "center");
document.getElementById('tela_menu').style.border = 1 + "px solid " + "#333333";
$("#tela_menu").css("border-radius", "10px");

if (navigator.geolocation) {
  navigator.geolocation.watchPosition(function (position) {
    latitude = position.coords.latitude;
    longitude = position.coords.longitude;
    velocidade = position.coords.speed;
    altitude = position.coords.altitude;
    latitude_usuario = latitude;
    longitude_usuario = longitude;
    localStorage.setItem('latitude', latitude);
    localStorage.setItem('longitude', longitude);
  }, function () {
    handleLocationError(true, infoWindow, map.getCenter());
  });
} else {
  // Browser doesn't support Geolocation
  handleLocationError(false, infoWindow, map.getCenter());
}

//feito com bootblocks.com.br
$("#loading").css("background-color", "rgba(0, 0, 0, 0)");
$("#loading").css("display", "flex");
$("#loading").css("justify-content", "center");
$("#" + 'loading').hide();

function onMapInitilize() {
  map.setOptions({ zoomControl: false });
  map.setOptions({ mapTypeControl: false });
  map.setOptions({ scaleControl: false });
  map.setOptions({ streetViewControl: false });
  function addAutocomplete() {
    var input = document.getElementById('box_origem');
    let radius = 10000;
    let center = new google.maps.LatLng(latitude_usuario, longitude_usuario);
    let circle = new google.maps.Circle({
      center: center,
      radius: radius
    });
    let options = {
      bounds: circle.getBounds()
    };
    autocomplete_box_origem = new google.maps.places.Autocomplete(input, options);
    autocomplete_box_origem.addListener("place_changed", () => {
      let place = autocomplete_box_origem.getPlace();
      
      // Validação: verificar se o lugar tem geometria (coordenadas)
  if (!place.geometry) {
    Swal.fire('Erro: ZERO_RESULTS', 'Não foi possível encontrar as coordenadas deste local. Tente um endereço mais específico.', 'error');
    document.getElementById('box_origem').value = '';
    return;
  }
  
  // Validação: verificar se não é apenas um país
  if (place.types && place.types.includes('country')) {
    Swal.fire('Endereço muito amplo', 'Por favor, selecione um endereço mais específico, como uma rua, bairro ou cidade, não apenas o país.', 'warning');
    document.getElementById('box_origem').value = '';
    return;
  }
      
      endereco_texto = place.formatted_address;
      lat_resposta_1 = place.geometry.location.lat();
      long_resposta_2 = place.geometry.location.lng();
      endereco_inicial = endereco_texto;
      latitude_inicial = lat_resposta_1;
      lonngitude_inicial = long_resposta_2;
      var marker = new google.maps.Marker({
        position: { lat: lat_resposta_1, lng: long_resposta_2 },
        map: map,
        icon: "assets/marcador.png",
        title: "Origem",
        marker_id: 1
      });
      marker.addListener("click", function () {
        let id = this.marker_id;
      });
      Makers.push(marker);
    });
  }
  addAutocomplete();
  proximo_input();
  ajax_post_async((String(url_principal) + 'get_all_motoristas.php'), { senha: senha, telefone: telefone }, mostrar_motoristas);
  temporizador_busca_motoristas = setInterval(function () {
    ajax_post_async((String(url_principal) + 'get_all_motoristas.php'), { senha: senha, telefone: telefone }, mostrar_motoristas);
  }, 10000);
};

//feito com bootblocks.com.br
$("#" + 'reprodutor_lottie_1').hide();
$("#" + 'reprodutor_lottie_2').hide();
$("#" + 'reprodutor_lottie_3').hide();
$("#" + 'tela_status').hide();
$("#" + 'tela_categorias').hide();

//feito com bootblocks.com.br
endereco_inicial = '';
endereco_final = '';
latitude_inicial = '';
lonngitude_inicial = '';
latitude_final = '';
longitude_final = '';


function qclick5() {
  let elementoClick = document.getElementById('tela_sair');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      localStorage.clear();
      window.location.href = "index.php";
    });
  }
}
qclick5();

// Funçao desativada para a função que verifica destino selecionado funcionar sem duplicidade para o botão avançar
//function qclick6() {
  //let elementoClick = document.getElementById('btn_avancar');
  //if (elementoClick) {
    //elementoClick.addEventListener("click", function () {
      //if (document.getElementById('box_origem').value && document.getElementById('box_destino').value) {
        //$("#" + 'loading').show();
        //ajax_post_async((String(url_principal) + 'calcular_custos.php'), { cidade_id: cidade_id, lat_ini: latitude_inicial, lng_ini: lonngitude_inicial, lat_fim: latitude_final, lng_fim: longitude_final }, exibir_categorias);
      //} else {
        //Swal.fire('Preencha origem e destino!');
      //}

    //});
  //}
//}
//qclick6();

//feito com bootblocks.com.br
$("#box_origem").css("border-radius", "15px");
$("#box_destino").css("border-radius", "15px");
$("#box_obs").css("border-radius", "15px");


function qclick7() {
  let elementoClick = document.getElementById('card_cancelar');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      Swal.fire({
        title: 'Deseja realmente cancelar a corrida?',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
      }).then((result) => {
        if (result.value) {
          cancelar()
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          n_cancelar()
        }
      });

    });
  }
}
qclick7();

//feito com bootblocks.com.br
$("#card_iniciar_chamado").css("background-color", "#000000");
$("#btn_avancar").css("background-color", "#000000");
$("#destinos_cabecalho").css("display", "flex");
$("#destinos_cabecalho").css("justify-content", "center");
$("#tela_lbl_destino").css("display", "flex");
$("#tela_lbl_destino").css("justify-content", "center");
$("#destinos_cabecalho").css("display", "flex");
$("#destinos_cabecalho").css("align-items", "center");
$("#btn_avancar").css("display", "flex");
$("#btn_avancar").css("justify-content", "center");
$("#card_iniciar_chamado").css("display", "flex");
$("#card_iniciar_chamado").css("justify-content", "center");
document.getElementById('btn_avancar').style.height = '80' + "px";
document.getElementById('btn_avancar').style.width = '90' + "%";
document.getElementById('btn_avancar').style.height = "auto";
document.getElementById('card_iniciar_chamado').style.height = '80' + "px";
document.getElementById('card_iniciar_chamado').style.width = '90' + "%";
document.getElementById('card_iniciar_chamado').style.height = "auto";
$("#btn_avancar").css("border-radius", "30px");
$("#card_iniciar_chamado").css("border-radius", "30px");
$("#btn_avancar").css("display", "flex");
$("#btn_avancar").css("align-items", "center");
$("#card_iniciar_chamado").css("display", "flex");
$("#card_iniciar_chamado").css("justify-content", "center");
$("#tela_btn_avancar").css("display", "flex");
$("#tela_btn_avancar").css("justify-content", "center");
$("#tela_card_iniciar_chamado").css("display", "flex");
$("#tela_card_iniciar_chamado").css("justify-content", "center");
$("#tela_lbl_categoria").css("display", "flex");
$("#tela_lbl_categoria").css("justify-content", "center");
$("#" + 'tela_lbl_categoria').css("padding-left", 0 + "px");
$("#" + 'tela_lbl_categoria').css("padding-right", 0 + "px");
$("#" + 'tela_lbl_categoria').css("padding-top", 10 + "px");
$("#" + 'tela_lbl_categoria').css("padding-bottom", 2 + "px");
$("#txt_tela_categorias").css("display", "flex");
$("#txt_tela_categorias").css("justify-content", "center");
$("#" + 'lbl_avancar').css("margin-left", 0 + "px");
$("#" + 'lbl_avancar').css("margin-right", 0 + "px");
$("#" + 'lbl_avancar').css("margin-top", 2 + "px");
$("#" + 'lbl_avancar').css("margin-bottom", 2 + "px");
$("#" + 'btn_avancar').css("margin-left", 0 + "px");
$("#" + 'btn_avancar').css("margin-right", 0 + "px");
$("#" + 'btn_avancar').css("margin-top", 0 + "px");
$("#" + 'btn_avancar').css("margin-bottom", 10 + "px");
$("#" + 'tela_destinos').hide();
$("#" + 'tela_btn_avancar').hide();
$("#" + 'tela_categorias').hide();
$('#tela_categorias').css({
  'max-height': '85%',
  'overflow-y': 'auto'
});

function qclick8() {
  let elementoClick = document.getElementById('card_iniciar');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#" + 'tela_barra_inicio').hide();
      document.getElementById('tela_destinos').style.position = "fixed";
      document.getElementById('tela_destinos').style.top = "0px";
      document.getElementById('tela_destinos').style.left = "0";
      document.getElementById('tela_destinos').style.right = "0";
      document.getElementById('tela_destinos').style.zIndex = "27";
      $("#" + 'tela_destinos').show();
      document.getElementById('tela_btn_avancar').style.position = "fixed";
      document.getElementById('tela_btn_avancar').style.bottom = "0px";
      document.getElementById('tela_btn_avancar').style.left = "0";
      document.getElementById('tela_btn_avancar').style.right = "0";
      document.getElementById('tela_btn_avancar').style.zIndex = "28";
      $("#" + 'tela_btn_avancar').show();
      obter_endereco_usuario();

    });
  }
}
qclick8();

//feito com bootblocks.com.br
$("#" + 'tela_barra_inicio').animate({ height: (window.innerHeight * (20 / 100)) + "px", width: (window.innerWidth * (100 / 100)) + "px" }, 800);
document.getElementById('tela_barra_inicio').style.position = "fixed";
document.getElementById('tela_barra_inicio').style.bottom = "0px";
document.getElementById('tela_barra_inicio').style.left = "0";
document.getElementById('tela_barra_inicio').style.right = "0";
document.getElementById('tela_barra_inicio').style.zIndex = "21";
$("#tela_boas_vindas").css("display", "flex");
$("#tela_boas_vindas").css("justify-content", "center");
$("#tela_onde_vamos").css("display", "flex");
$("#tela_onde_vamos").css("justify-content", "center");
$("#tela_card_iniciar").css("display", "flex");
$("#tela_card_iniciar").css("justify-content", "center");
verificar_saudacao();
$("#" + 'lbl_boas_vindas').css("margin-left", 0 + "px");
$("#" + 'lbl_boas_vindas').css("margin-right", 0 + "px");
$("#" + 'lbl_boas_vindas').css("margin-top", 10 + "px");
$("#" + 'lbl_boas_vindas').css("margin-bottom", 0 + "px");
$("#card_iniciar").css("display", "flex");
$("#card_iniciar").css("justify-content", "center");
document.getElementById('card_iniciar').style.height = '80' + "px";
document.getElementById('card_iniciar').style.width = '80' + "%";
document.getElementById('card_iniciar').style.height = "auto";
$("#card_iniciar").css("border-radius", "30px");
$("#card_iniciar").css("display", "flex");
$("#card_iniciar").css("align-items", "center");
$("#" + 'lbl_onde_card_iniciar').css("margin-left", 0 + "px");
$("#" + 'lbl_onde_card_iniciar').css("margin-right", 0 + "px");
$("#" + 'lbl_onde_card_iniciar').css("margin-top", 2 + "px");
$("#" + 'lbl_onde_card_iniciar').css("margin-bottom", 2 + "px");


function qclick9() {
  let elementoClick = document.getElementById('icone_chat');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "mensagens.php";
    });
  }
}
qclick9();

//feito com bootblocks.com.br
aceirou_aviso = false;


function qclick10() {
  let elementoClick = document.getElementById('card_iniciar_chamado');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      forma_pagamento = $("input[name=forma_pagamento]:checked").val();
      if (forma_pagamento) {
        if (forma_pagamento == 'Carteira Crédito') {
          ajax_post_async((String(url_principal) + 'verifica_saldo.php'), { senha: senha, telefone: telefone, valor: valor_corrida }, verifica_saldo);
        } else {
          enviar_solicitacao_chamado();
        }
      } else {
        Swal.fire('Selecione a forma de Pagamento!');
      }

    });
  }
}
qclick10();

//feito com bootblocks.com.br
$("#tela_timer").css("display", "flex");
$("#tela_timer").css("justify-content", "center");
$("#card_cancelar").css("background-color", "000000");
$("#tela_status_txt").css("display", "flex");
$("#tela_status_txt").css("justify-content", "center");
$("#tela_botoes_status").css("display", "flex");
$("#tela_botoes_status").css("justify-content", "center");
$("#card_cancelar").css("display", "flex");
$("#card_cancelar").css("justify-content", "center");
document.getElementById('tela_status').style.position = "fixed";
document.getElementById('tela_status').style.bottom = "0px";
document.getElementById('tela_status').style.left = "0";
document.getElementById('tela_status').style.right = "0";
document.getElementById('tela_status').style.zIndex = "28";
document.getElementById('card_cancelar').style.height = '80' + "px";
document.getElementById('card_cancelar').style.width = '80' + "%";
document.getElementById('card_cancelar').style.height = "auto";
$("#card_cancelar").css("border-radius", "30px");
$("#card_cancelar").css("display", "flex");
$("#card_cancelar").css("align-items", "center");
$("#" + 'txt_cancelar').css("margin-left", 0 + "px");
$("#" + 'txt_cancelar').css("margin-right", 0 + "px");
$("#" + 'txt_cancelar').css("margin-top", 2 + "px");
$("#" + 'txt_cancelar').css("margin-bottom", 2 + "px");
$("#" + 'tela_status').hide();
$("#tela_lottie").css("display", "flex");
$("#tela_lottie").css("justify-content", "center");
$("#" + 'tela_lottie').css("margin-left", 0 + "px");
$("#" + 'tela_lottie').css("margin-right", 0 + "px");
$("#" + 'tela_lottie').css("margin-top", 10 + "px");
$("#" + 'tela_lottie').css("margin-bottom", 5 + "px");
$("#" + 'reprodutor_lottie_2').hide();
$("#" + 'reprodutor_lottie_3').hide();
$("#" + 'reprodutor_lottie_4').hide();
$("#tela_img_motorista").css("display", "flex");
$("#tela_img_motorista").css("justify-content", "center");
$("#tela_dados_motorista").css("display", "flex");
$("#tela_dados_motorista").css("justify-content", "center");
$("#" + 'tela_img_motorista').hide();
$("#" + 'tela_dados_motorista').hide();
$("#" + 'audio').hide();
$("#tela_txt_finalizar").css("display", "flex");
$("#tela_txt_finalizar").css("justify-content", "center");
$("#" + 'tela_txt_finalizar').hide();

//feito com bootblocks.com.br
$("#card_finalizar").css("background-color", "000000");
$("#card_finalizar").css("display", "flex");
$("#card_finalizar").css("justify-content", "center");
document.getElementById('card_finalizar').style.height = '80' + "px";
document.getElementById('card_finalizar').style.width = '80' + "%";
document.getElementById('card_finalizar').style.height = "auto";
$("#card_finalizar").css("border-radius", "30px");
$("#card_finalizar").css("display", "flex");
$("#card_finalizar").css("align-items", "center");
$("#" + 'card_finalizar').css("margin-left", 0 + "px");
$("#" + 'card_finalizar').css("margin-right", 0 + "px");
$("#" + 'card_finalizar').css("margin-top", 2 + "px");
$("#" + 'card_finalizar').css("margin-bottom", 2 + "px");
$("#" + 'card_finalizar').hide();
$("#tentar_novamente").css("display", "flex");
$("#tentar_novamente").css("align-items", "center");
$("#tentar_novamente").css("display", "flex");
$("#tentar_novamente").css("justify-content", "center");
$("#" + 'tentar_novamente').css("margin-left", 0 + "px");
$("#" + 'tentar_novamente').css("margin-right", 0 + "px");
$("#" + 'tentar_novamente').css("margin-top", 2 + "px");
$("#" + 'tentar_novamente').css("margin-bottom", 2 + "px");
$("#tentar_novamente").css("border-radius", "30px");
$("#" + 'tentar_novamente').hide();

$("#btn_avancar_escolha").css("border-radius", "15px");
$("#btn_avancar_escolha").css("display", "flex");
$("#btn_avancar_escolha").css("justify-content", "center");
$("#btn_avancar_escolha").css("display", "flex");
$("#btn_avancar_escolha").css("align-items", "center");
document.getElementById('btn_avancar_escolha').style.height = '100' + "px";
document.getElementById('btn_avancar_escolha').style.width = '90' + "%";
document.getElementById('btn_avancar_escolha').style.height = "auto";
selecionando_endereco = '';


function qclick11() {
  let elementoClick = document.getElementById('icone_voltar_destinos');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      $("#" + 'tela_destinos').hide();
      $("#" + 'tela_btn_avancar').hide();
      $("#" + 'tela_barra_inicio').show();

    });
  }
}
qclick11();


function qclick12() {
  let elementoClick = document.getElementById('tela_historico_corridas');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "historico.php";
    });
  }
}
qclick12();

//feito com bootblocks.com.br
$("#tela_cabecalho_status").css("display", "flex");
$("#tela_cabecalho_status").css("justify-content", "center");
$("#" + 'icone_minimizar').css("margin-left", 0 + "px");
$("#" + 'icone_minimizar').css("margin-right", 10 + "px");
$("#" + 'icone_minimizar').css("margin-top", 10 + "px");
$("#" + 'icone_minimizar').css("margin-bottom", 0 + "px");
status_minimizado = false;

//feito com bootblocks.com.br
$("#tela_cabecalho_categorias").css("display", "flex");
$("#tela_cabecalho_categorias").css("justify-content", "center");
$("#" + 'icone_minimizar_categorias').css("margin-left", 0 + "px");
$("#" + 'icone_minimizar_categorias').css("margin-right", 10 + "px");
$("#" + 'icone_minimizar_categorias').css("margin-top", 10 + "px");
$("#" + 'icone_minimizar_categorias').css("margin-bottom", 0 + "px");
categorias_minimizado = false;


function qclick13() {
  let elementoClick = document.getElementById('icone_minimizar');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      if (status_minimizado) {
        status_minimizado = false;
        $("#" + 'tela_status').animate({ height: altura_tela_status + "px", width: (window.innerWidth * (100 / 100)) + "px" }, 800);
        $("#icone_minimizar").html('close_fullscreen');
      } else {
        status_minimizado = true;
        altura_tela_status = document.getElementById('tela_status').offsetHeight;
        $("#" + 'tela_status').animate({ height: 50 + "px", width: (window.innerWidth * (100 / 100)) + "px" }, 800);
        $("#icone_minimizar").html('open_in_full');
      }

    });
  }
}
qclick13();

//feito com bootblocks.com.br
tamanho_msg = 0;
$("#" + 'audio_message').hide();
status_anterior = '';


function qclick14() {
  let elementoClick = document.getElementById('icone_minimizar_categorias');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      if (categorias_minimizado) {
        categorias_minimizado = false;
        $("#" + 'tela_categorias').animate({ height: altura_tela_categorias + "px", width: (window.innerWidth * (100 / 100)) + "px" }, 800);
        $("#icone_minimizar_categorias").html('close_fullscreen');
      } else {
        altura_tela_categorias = document.getElementById('tela_categorias').offsetHeight;
        categorias_minimizado = true;
        $("#" + 'tela_categorias').animate({ height: 50 + "px", width: (window.innerWidth * (100 / 100)) + "px" }, 800);
        $("#icone_minimizar_categorias").html('open_in_full');
      }

    });
  }
}
qclick14();


function qclick15() {
  let elementoClick = document.getElementById('tentar_novamente');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "home.php";
    });
  }
}
qclick15();


function qclick16() {
  let elementoClick = document.getElementById('card_finalizar');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "home.php";
    });
  }
}
qclick16();

// Função do cupom — protegida
function cupom() {
  const campoCupom = document.getElementById('cupom_desconto_input');
  const lblStatus = document.getElementById('lbl_status_cupom');
  const telaCategorias = document.getElementById('tela_categorias');

  // ✅ Garante que os elementos existem antes de usar
  if (!campoCupom || !lblStatus) {
    console.warn("Bloco de cupom desativado — função ignorada.");
    return;
  }

  const codigo_cupom = campoCupom.value.trim();

  if (!codigo_cupom) {
    lblStatus.innerHTML = "Por favor, insira um código de cupom válido.";
    lblStatus.style.display = 'block';
    lblStatus.style.color = 'red';
    return;
  }

  // Envia para valida_cupon.php
  ajax_post_async(
    String(url_principal) + 'valida_cupon.php',
    { senha: senha, telefone: telefone, cupom: codigo_cupom, valor: valor_corrida },
    function (data) {
      try {
        const qretorno = JSON.parse(data);
        if (qretorno.desconto && txt_to_number(qretorno.desconto) > 0) {
          lblStatus.innerHTML = qretorno.status + " Valor final: R$ " + qretorno.desconto;
          lblStatus.style.color = "green";
          valor_corrida = qretorno.desconto;
          $("#txt_card_chamado").html('Confirmar com desconto R$ ' + qretorno.desconto);
          // Bloqueia botão e campo
          if (btn_cupom) btn_cupom.disabled = true;
          campoCupom.disabled = true;
          // Impede mudar categoria
          podeAlterarCategoria = false;
        } else {
          lblStatus.innerHTML = qretorno.status;
          lblStatus.style.color = "red";
        }
      } catch (e) {
        console.error("Erro ao processar retorno do cupom:", e);
        lblStatus.innerHTML = "Erro ao validar o cupom.";
        lblStatus.style.color = "red";
      }
      lblStatus.style.display = 'block';
      if (telaCategorias) $("#tela_categorias").animate({ height: "+=20px" }, 200);
    }
  );
}

// ✅ Só adiciona o evento se o botão existir
const btn_cupom = document.getElementById('btn_aplicar_cupom');
if (btn_cupom) {
  btn_cupom.addEventListener("click", cupom);
} else {
  console.warn("Botão de cupom não encontrado — bloco desativado.");
}

function ajax_post(url, dados) {
  let retorno;
  $.ajax({
    url: url,
    type: "POST",
    data: dados,
    async: false,
    success: function (data) {
      retorno = data;
    },
    error: function (data) {
      retorno = data;
    }
  });
  return retorno;
} 
function ajax_post_async(url, dados, funcao_chamar) {
  $.ajax({
    url: url,
    type: "POST",
    data: dados,
    async: true,
    success: function (data) {
      funcao_chamar(data);
    },
    error: function (data) {
      funcao_chamar(data);
    }
  });
}
function txt_to_number(txt) {
  txt = txt + "";
  if (txt.includes(",")) {
    txt = txt.replace(",", ".");
  }
  if (txt.includes(".")) {
    txt = parseFloat(txt);
  } else {
    txt = parseInt(txt);
  }
  return txt;
}





$(document).ready(function () {
  $("#loading-page-bb").css("opacity", "1");
});


$("#" + 'tela_base_escolha').hide();


/**********************************************
 * Proteção contra texto "undefined Km / Min"
 * Cole este bloco no final de home.js
 **********************************************/
(function () {
  // Função utilitária segura para formatar distância/tempo
  window.safeFormatDistance = function (km, minutos) {
    // normaliza
    if (km === null || typeof km === "undefined" || km === "") return "Calculando distância...";
    if (minutos === null || typeof minutos === "undefined" || minutos === "") return "Calculando tempo...";
    return "A " + km + " Km e " + minutos + " Min";
  };

  // Substitui ocorrências já presentes no DOM
  function sanitizeExistingDistanceText() {
    try {
      // procura textos que contenham 'undefined' junto com Km ou Min
      const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
      let node;
      while ((node = walker.nextNode())) {
        if (node.nodeValue && /undefined\s*Km|undefined\s*Min/i.test(node.nodeValue)) {
          console.warn("Sanitizando nó com 'undefined' encontrado:", node.nodeValue);
          // substitui por algo neutro
          node.nodeValue = node.nodeValue.replace(/undefined\s*Km/i, "calculando Km");
          node.nodeValue = node.nodeValue.replace(/undefined\s*Min/i, "calculando Min");
        }
      }
    } catch (e) {
      console.error("Erro ao sanitizar textos de distância:", e);
    }
  }

  // Observador que escuta mudanças no DOM e corrige textos recém-inseridos
  const observer = new MutationObserver(function (mutations) {
    for (const m of mutations) {
      // checa nodes adicionados
      if (m.addedNodes && m.addedNodes.length) {
        m.addedNodes.forEach(node => {
          try {
            if (node.nodeType === Node.TEXT_NODE) {
              if (/undefined\s*Km|undefined\s*Min/i.test(node.nodeValue)) {
                console.warn("Sanitizando texto adicionado (text node):", node.nodeValue);
                node.nodeValue = node.nodeValue.replace(/undefined\s*Km/i, "calculando Km");
                node.nodeValue = node.nodeValue.replace(/undefined\s*Min/i, "calculando Min");
              }
            } else if (node.nodeType === Node.ELEMENT_NODE) {
              // verifica o texto interno do elemento
              const txt = node.innerText || node.textContent || "";
              if (/undefined\s*Km|undefined\s*Min/i.test(txt)) {
                console.warn("Sanitizando texto adicionado (element):", txt, node);
                node.innerHTML = node.innerHTML.replace(/undefined\s*Km/gi, "calculando Km");
                node.innerHTML = node.innerHTML.replace(/undefined\s*Min/gi, "calculando Min");
              }
            }
          } catch (e) {
            console.error("Erro ao processar node adicionado no observer:", e);
          }
        });
      }
    }
  });

  // Inicia o observer
  observer.observe(document.body, {
    childList: true,
    subtree: true,
    characterData: true
  });

  // Sanitiza o que já existe ao carregar
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", sanitizeExistingDistanceText);
  } else {
    sanitizeExistingDistanceText();
  }

  // Exponha função de diagnóstico para localizar onde o texto é montado
  window.findDistancePlaceholders = function () {
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
    let node;
    while ((node = walker.nextNode())) {
      if (node.nodeValue && /undefined\s*Km|undefined\s*Min/i.test(node.nodeValue)) {
        results.push({ text: node.nodeValue.trim(), node });
      }
    }
    console.log("Encontrados placeholders:", results);
    return results;
  };

  console.info("Sanitizador de distância ativo (corrige 'undefined Km/Min' automaticamente).");
})();