<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
if(isset($$cidade_id) || $cidade_id == 0){?>
<div class="alert alert-danger" role="alert">
  Faça login em uma cidade primeiro! <a href="../admin/cidades.php?redirect=<?php echo $pagina_atual; ?>" class="alert-link">clique aqui</a> e escolha uma cidade.
</div>
<?
include_once("dep_query.php");
exit();
}
?>