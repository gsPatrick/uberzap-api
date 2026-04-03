<?php include_once 'seguranca.php'; ?>


  <style>
    /* Seus estilos personalizados aqui */

    /* Exemplo de estilo para o menu principal */
    .navbar {
      background-color: #000000;
    }

    .navbar-toggler-icon {
      background-color: white;
    }

    .navbar-brand img {
      width: 150px;
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
    .navbar-toggler-icon {
    background-color: #000000; /* Mude para a cor desejada */
  }
  </style>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="dash.php">
      <img src="../assets/img/logo.png" alt="Logo">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="dash.php">Início<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="corridas.php">Corridas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="novo_chamado.php">Novo Chamado</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="historico.php">Histórico</a>
        </li>
        <?php if($admin == 1){ ?>
          <li class="nav-item">
          <a class="nav-link" href="cidades.php">Cidades</a>
        </li>
        <?php } ?>
        <li class="nav-item">
          <a class="nav-link" href="franqueados.php">Franqueados</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRelatorios" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Relatórios
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownRelatorios">
            <a class="dropdown-item" href="relatorio_resumido.php">Relatório Resumido</a>
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

