var latitude, longitude, velocidade, altitude, dados_retorno, retorno, lista_de_cidades, nome, token, email, url_principal, telefone, senha, Item, senha_2, cidade_id;

function cadastro_ok() {
  Swal.fire('Cadastrado com sucesso!');
  var temp = setInterval(nova_tela, 1000);
}

function nova_tela() {
  window.location.href = "principal.php";
}

function cadastrar() {
  nome = document.getElementById('nome_box').value.trim();
  email = document.getElementById('email_box').value.trim().toLowerCase();
  telefone = document.getElementById('telefone_box').value.trim();
  senha = document.getElementById('senha_box').value;
  senha_2 = document.getElementById('senha_box_2').value;
  cidade_id = document.getElementById('cidades').value;

  // Validação do nome
  if (!nome.length) {
    Swal.fire('Erro. Digite o seu nome completo');
    return;
  }
  if (nome.toLowerCase() === "teste") {
    Swal.fire('Erro. Nome inválido');
    return;
  }

  // Validação do email
  if (!email.length) {
    Swal.fire('Erro. E-mail obrigatório');
    return;
  }
  if (email.includes('@email') || email.includes('@teste') || email.includes('teste@')) {
    Swal.fire('Erro. E-mail inválido');
    return;
  }

  // Validação do telefone
  if (!/^\d{11}$/.test(telefone)) {
    Swal.fire('O telefone deve conter 11 números incluindo DDD, sem letras ou símbolos');
    return;
  }

  // Validação da senha
  if (!senha.length) {
    Swal.fire('Preencha com sua Senha');
    return;
  }
  if (senha !== senha_2) {
    Swal.fire('Senhas são diferentes!');
    return;
  }

  // Validação da cidade
  if (cidade_id == 0) {
    Swal.fire('Selecione uma Cidade!');
    return;
  }

  document.getElementById('loading').style.position = "fixed";
  document.getElementById('loading').style.top = "50%";
  document.getElementById('loading').style.transform = "translateY(-50%)";
  document.getElementById('loading').style.left = "0";
  document.getElementById('loading').style.right = "0";
  document.getElementById('loading').style.zIndex = "20";
  $("#loading").show();

  ajax_post_async((String(url_principal) + 'cadastra_user.php'), {
    token: token,
    nome: nome,
    email: email,
    telefone: telefone,
    senha: senha,
    cidade_id: cidade_id,
    latitude: latitude,
    longitude: longitude
  }, finaliza_cadastro);
}

function listar_cidades(dados_retorno) {
  $("#cidades").empty();
  $("#cidades").append("<option value='0' selected>Cidade</option>");
  lista_de_cidades = JSON.parse(dados_retorno);
  for (var Item_index in lista_de_cidades) {
    Item = lista_de_cidades[Item_index];
    $("#cidades").append("<option value='" + Item['id'] + "'>" + Item['nome'] + "</option>");
  }
}

function finaliza_cadastro(retorno) {
  $("#loading").hide();
  retorno = JSON.parse(retorno);
  if (retorno['status'] === 'sucesso') {
    Swal.fire('Cadastrado com sucesso!');
    var fechar = setInterval(salvar_local, 1000);
  } else {
    Swal.fire({
      icon: 'error',
      title: retorno['status'],
      text: 'Erro'
    });
  }
}

function salvar_local() {
  localStorage.setItem('nome_cliente', nome);
  localStorage.setItem('email_cliente', email);
  localStorage.setItem('telefone_cliente', telefone);
  localStorage.setItem('senha', senha);
  localStorage.setItem('cidade_id', cidade_id);
  window.location.href = "login.php";
}

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function (position) {
  }, function (error) {
  });
} else {
  alert("Seu navegador não suporta Geolocalização!");
}

$("#loading").css("background-color", "rgba(0, 0, 0, 0)");
$("#loading").css("display", "flex");
$("#loading").css("justify-content", "center");
$("#loading").hide();

$("#nome_box").css("border-radius", "15px");
$("#email_box").css("border-radius", "15px");
$("#telefone_box").css("border-radius", "15px");
$("#senha_box").css("border-radius", "15px");
$("#senha_box_2").css("border-radius", "15px");
$("#logar_btn").css("border-radius", "15px");
$("#cidades").css("border-radius", "15px");

if (navigator.geolocation) {
  navigator.geolocation.watchPosition(function (position) {
    latitude = position.coords.latitude;
    longitude = position.coords.longitude;
    velocidade = position.coords.speed;
    altitude = position.coords.altitude;
    localStorage.setItem('latitude', latitude);
    localStorage.setItem('longitude', longitude);
  }, function () {
    handleLocationError(true, infoWindow, map.getCenter());
  });
} else {
  handleLocationError(false, infoWindow, map.getCenter());
}

lista_de_cidades = [];
token = localStorage.getItem('token') || '';
url_principal = localStorage.getItem('url_principal') || '';
ajax_post_async((String(url_principal) + 'get_cidades.php'), { token: token }, listar_cidades);

function qclick() {
  let elementoClick = document.getElementById('logar_lbl');
  if (elementoClick) {
    elementoClick.addEventListener("click", function () {
      window.location.href = "login.php";
    });
  }
}
qclick();

$("#tela_logo").css("display", "flex");
$("#tela_logo").css("justify-content", "center");
$("#tela_txt_cadastro").css("display", "flex");
$("#tela_txt_cadastro").css("justify-content", "center");
$("#logar_btn").css("height", "40px");
$("#logar_btn").css("width", "100%");
$("#tela_cadastrar").css("display", "flex");
$("#tela_cadastrar").css("justify-content", "center");
$("body").css("background-color", "#ffffff");
$("#tela_logo").css("background-color", "#ffffff");
$("#tela_txt_cadastro").css("background-color", "#ffffff");
$("#tela_cadastrar").css("background-color", "#ffffff");

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

$(document).ready(function () {
  $("#loading-page-bb").css("opacity", "1");
});
