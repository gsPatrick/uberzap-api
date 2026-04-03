<?
include("seguranca.php");
include "../classes/taximetro.php";
$t = new Taximetro();

if(isset($_POST['salvar_btn'])){
    $tx_minima = $_POST['tx_minima'];
    $tx_minuto = $_POST['tx_minuto'];
    $tx_km = $_POST['tx_km'];
    $t->insere($cidade_id, $tx_minima, $tx_minuto, $tx_km);
    echo "<script>alert('Taxas salvas com sucesso!');</script>";
}
$taxas = $t->getByCidadeId($cidade_id);
?>
<!doctype html>
<html lang="pt-br">
<?php include "head.php"; ?>
<?php include("menu.php"); ?>

<body>
    <div class="container-fluid">
    </div>
    <div class="container">
        <div class="container-principal-produtos">
            <h4 class="page-header">TAXAS DO TAXÍMETRO</h4>
            <hr>
            <form action="" method="POST" enctype="multipart/form-data" name="upload">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Taxa Base:</label>
                        <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_minima" onkeyup="formatarMoeda('tx_minima')" id="tx_minima" value="<?php echo $taxas['tx_minima']; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Valor por Minuto:</label>
                        <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_minuto" onkeyup="formatarMoeda('tx_minuto')" id="tx_minuto" value="<?php echo $taxas['tx_minuto']; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Valor por Km:</label>
                        <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" name="tx_km" onkeyup="formatarMoeda('tx_km')" id="tx_km" value="<?php echo $taxas['tx_km']; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <button type="submit" name="salvar_btn" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
<?php include("dep_query.php"); ?>
<script>
    //formatar valor para moeda 10,00
    function formatarMoeda(elemento) {
        var elemento = document.getElementById(elemento);
        var valor = elemento.value;

        valor = valor + '';
        valor = valor.replace(/[\D]+/g, '');
        valor = valor.padStart(3, '0');  // Ensure at least 3 digits
        valor = (parseInt(valor) / 100).toFixed(2);  // Convert to decimal
        valor = valor.replace('.', ',');  // Replace decimal point with comma

        elemento.value = valor;
    }

</script>