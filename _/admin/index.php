<?php 
include '../bd/conexao.php';
include_once '../bd/config.php';
?>

<!DOCTYPE HTML>
<html lang="pt-br">
	<head><meta charset="utf-8">
		<title> <?php echo APP_NAME; ?> </title>
		
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="Página Inicial">
		<!--LINK CSS-->
		<link rel="stylesheet" type="text/css" href="../css/style-css.css">
		<!--LINK CDN BOOTSTRAP-->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	</head>
	<body style="background: url(../assets/img/fundo.jpg);
	width: 100%;
 	height: 100%;
    background-position:68%;
	background-repeat: no-repeat;
	background-size: cover;
    background-attachment:fixed;">
		<div class="container-principal" style="margin-top: 5%;" align="center">
			<header>
				<h1 class="page-header" align="center"><img src="../assets/img/logo.png" width="200px;"></h1>
			</header>
			<main>
				<div class="form_acesso">
					<form action="acesso.php" method="POST">
						<div class="form-group col-md-6">
							<label name="usuario"></label>
							<input class="form-control form-control-sm" type="user" name="usuario" placeholder="Usuário" required>
						</div>
						<div class="form-group col-md-6">
							<label name="senha"></label>
							<input class="form-control form-control-sm col-md-12" type="password" name="senha" placeholder="Senha" required>
						</div>
						</br>
						<input type="submit" name="btn_enviar" class="btn btn-dark btn-sm col-md-5" value="ENTRAR"/>
					</form>	
				</div>
				<div>
					<br>
					<br>
			</main>
			</div>
			</footer>
		</div>
	</body>