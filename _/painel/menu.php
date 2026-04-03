<?php
$ano = date("Y");
?>
<style>
  /* Exemplo de estilo para o menu principal */
  .navbar {
    background-color: #000000;
  }

  .navbar-toggler-icon {
    background-color: white;
  }

  .navbar-brand img {
    width: 100px;
  }

  .navbar-nav .nav-item .nav-link {
    color: white;
    transition: color 0.3s;
  }

  .navbar-nav .nav-item .nav-link:hover {
    color: #ff9900;
  }

  /* Estilo para os itens do menu dropdown */
  .dropdown-menu {
    background-color: #000000;
    border: none;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  }

  .dropdown-item {
    color: white;
    transition: background-color 0.3s;
  }

  .dropdown-item:hover {
    background-color: #ff9900;
  }
</style>
<nav class="navbar navbar-expand-lg navbar-dark">
  <a class="navbar-brand" href="corridas.php">
    <img src="../assets/img/logo.png" alt="Logo">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a id="menu-controle" class="nav-link" href="dash.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a id="menu-novo-chamado" class="nav-link" href="novo_chamado.php">Novo Chamado</a>
      </li>
      <li class="nav-item">
        <a id="menu-novo-chamado" class="nav-link" href="corridas.php">Corridas</a>
      </li>
      <li class="nav-item">
        <a id="menu-historico" class="nav-link" href="historico.php">Histórico</a>
      </li>

      <!-- Insert the dropdown menu for Motoristas here -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="menu-motoristas" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Motoristas
        </a>
        <div class="dropdown-menu" aria-labelledby="menu-motoristas">
          <a class="dropdown-item" style="color: white;" href="cadastra_motorista.php">Cadastro de Motoristas</a>
          <a class="dropdown-item" style="color: white;" href="listar_motoristas.php">Motoristas Ativos</a>
          <a class="dropdown-item" style="color: white;" href="listar_motoristas_off.php">Motoristas Desativados</a>
          <a class="dropdown-item" style="color: white;" href="relatorio_motoristas.php">Relatório Motoristas</a>
          <a class="dropdown-item" style="color: white;" href="lista_motoristas_temp.php">Motoristas aguardando aprovação</a>
          <a class="dropdown-item" style="color: white;" href="transacoes_motoristas.php">Transações</a>
        </div>
      </li>
      <!-- Insert the dropdown menu for Clientes here -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="menu-clientes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Clientes
        </a>
        <div class="dropdown-menu" aria-labelledby="menu-clientes">
          <a class="dropdown-item" style="color: white;" href="listar_clientes.php">Clientes</a>
          <a class="dropdown-item" style="color: white;" href="transacoes.php">Transações</a>
        </div>
      </li>

      <li class="nav-item">
        <a id="menu-relatorio-geral" class="nav-link" href="categorias.php">Categorias</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="menu-clientes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dinâmico
        </a>
        <div class="dropdown-menu" aria-labelledby="menu-clientes">
          <a class="dropdown-item" style="color: white;" href="dinamico_horario.php">Por Horário</a>
          <a class="dropdown-item" style="color: white;" href="dinamico_mapa.php">Por Região</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="menu-clientes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Configurações
        </a>
        <div class="dropdown-menu" aria-labelledby="menu-clientes">
          <a class="dropdown-item" style="color: white;" href="configuracoes_pagamento.php">Configurações de Pagamento</a>
          <a class="dropdown-item" style="color: white;" href="taximetro.php">Configurações do Taxímetro</a>
          <a class="dropdown-item" style="color: white;" href="banners.php">Banners</a>
          <a class="dropdown-item" style="color: white;" href="listar_cupons.php">Cupons de Desconto</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="mapa_motoristas.php">Mapa</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="destruirSessao.php">Sair</a>
      </li>
    </ul>
  </div>
</nav>