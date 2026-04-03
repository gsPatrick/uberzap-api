<?
include("seguranca.php");
include("../bd/config.php");
include("../classes/categorias.php");
include("../classes/cidades.php");

$c = new categorias();
$ct = new cidades();
$categorias = $c->get_categorias($cidade_id);

$dados_cidade = $ct->get_dados_cidade($cidade_id);
$lat_ini = $dados_cidade['latitude'];
$lng_ini = $dados_cidade['longitude'];

?>
<!doctype html>
<html lang="pt-br">
<?php include("head.php"); ?>
<?php include("menu.php"); ?>
<?php include_once "external/verifica_login.php"; ?>

<body>
    <div class="container-fluid">
    </div>
    <div class="container">
        <div class="container-principal-produtos">
            <h4 class="page-header">Novo Chamado</h4>
            <hr>
            <div class="row">
                <div class="form-group col-md-6">
                    <form action="nov_chamado.php" id="formulario" method="POST" enctype="multipart/form-data" name="upload">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Nome do Passageiro:</label>
                                <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="nome" name="nome" placeholder="Nome do Passageiro" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Categoria:</label>
                                <select class="form-control form-control-sm col-md-12 col-sm-12" name="categoria_id" id="categoria_id">
                                    <option value="0">Selecione</option>
                                    <?php foreach ($categorias as $categoria) { ?>
                                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nome']; ?></option>
                                    <?php } ?>
                                </select> 
                            </div>
                        </div>
                        <div id="endereco-div" style="display: none;">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Endereço Partida:</label>
                                    <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="endereco_ini" name="endereco_ini" placeholder="Endereço Inicio" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Endereço Chegada:</label>
                                    <input class="form-control form-control-sm col-md-09 col-sm-09" type="text" id="endereco_fim" name="endereco_fim" placeholder="Endereço Fim" />
                                </div>
                            </div>
                        </div>
                    </form>
                    <button href="#" type="button" id="btn-enviar" onclick="enviarCorrida()" class="btn btn-primary" disabled >Enviar</button>
                    <button href="#" type="button" onclick="estimarTaxas()" id="estimar-taxas" class="btn btn-outline-success" disabled >Estimar Taxas</button>
                </div>
                <div class="col-md-6">
                    <div class="card" id="card-estimativas" style="width: 30rem;">
                        <div class="card-header">
                            Estimativas:
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Valor Total: R$ <span id="total">0,00</span> </li>
                            <li class="list-group-item">Tempo estimado: <span id="tempo">0:00</span> </li>
                            <li class="list-group-item">Km do trajeto <span id="km">0</span> </li>
                        </ul>
                    </div>
                </div>
                <br>

            </div>
        </div>
        <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo KEY_GOOGLE_MAPS; ?>&libraries=places">
        </script>
        <!--Fechando container bootstrap-->
        <?php include("dep_query.php"); ?>
</body>
<script>
    var url = '<?php echo DOMINIO; ?>';
    var categorias = '<?php echo json_encode($categorias); ?>';
    categorias = JSON.parse(categorias);
    var latitude_central = '<?php echo $lat_ini; ?>';
    var longitude_central = '<?php echo $lng_ini; ?>';
    formulario = document.getElementById('formulario');
    var raioPesquisa = 0;
    var cidade_id = <?php echo $cidade_id; ?>;
    var categoria_id = 0;

    var lat_ini = 0;
    var lng_ini = 0;
    var lat_fim = 0;
    var lng_fim = 0;
    var endereco_ini = "";
    var endereco_fim = "";
    var nome = "";
    var km = 0;
    var tempo = 0;
    var taxa = 0;

    $('#categoria_id').on('change', function() {
        categoria_id = $(this).val();
        let categoria = categorias.find(categoria => categoria.id == categoria_id);
        raioPesquisa = categoria.raio;
        //to int
        raioPesquisa = parseInt(raioPesquisa);
        $('#endereco-div').show();
        $('#estimar-taxas').removeAttr('disabled');

        var pontoDePartida = new google.maps.LatLng(latitude_central, longitude_central);

        // Crie um objeto LatLngBounds para definir a área de pesquisa com base no ponto de partida e raio
        var limites = new google.maps.LatLngBounds();
        limites.extend(pontoDePartida);


        // Expanda os limites para incluir uma área que cobre o raio de pesquisa
        var raioCircle = new google.maps.Circle({
            center: pontoDePartida,
            radius: raioPesquisa
        });
        limites.union(raioCircle.getBounds());

        // Inicialize o Autocomplete para o campo de endereço de partida com os limites definidos
        var autocompletePartida = new google.maps.places.Autocomplete(document.getElementById('endereco_ini'), {
            bounds: limites
        });

        var autocompleteChegada = new google.maps.places.Autocomplete(document.getElementById('endereco_fim'), {
            bounds: limites
        });
        autocompletePartida.addListener('place_changed', function() {
            let place = autocompletePartida.getPlace();
            lat_ini = place.geometry.location.lat();
            lng_ini = place.geometry.location.lng();
        });

        autocompleteChegada.addListener('place_changed', function() {
            let place = autocompleteChegada.getPlace();
            lat_fim = place.geometry.location.lat();
            lng_fim = place.geometry.location.lng();
        });
    });


    estimarTaxas = function() {
        endereco_ini = $('#endereco_ini').val();
        endereco_fim = $('#endereco_fim').val();
        if (endereco_ini == "" || endereco_fim == "") {
            alert("Preencha os endereços");
            return;
        }
        if (lat_ini == 0 || lng_ini == 0 || lat_fim == 0 || lng_fim == 0) {
            alert("Selecione os endereços");
            return;
        }
        if (categoria_id == 0) {
            alert("Selecione a categoria");
            return;
        }
        getTaxa();
    }

    function enviarCorrida(){
        if (endereco_ini == "" || endereco_fim == "") {
            alert("Preencha os endereços");
            return;
        }
        if (lat_ini == 0 || lng_ini == 0 || lat_fim == 0 || lng_fim == 0) {
            alert("Selecione os endereços");
            return;
        }
        if (categoria_id == 0) {
            alert("Selecione a categoria");
            return;
        }
        nome = $('#nome').val();
        $.ajax({
            url: url + "/_/funcoes/insere_corrida.php",
            type: 'post',
            data: {
                nome: nome,
                lat_ini: lat_ini,
                lng_ini: lng_ini,
                lat_fim: lat_fim,
                lng_fim: lng_fim,
                endereco_ini: endereco_ini,
                endereco_fim: endereco_fim,
                categoria_id: categoria_id,
                cidade_id: cidade_id,
                km : km,
                tempo: tempo,
                taxa : taxa
            },
            success: function(data) {
                console.log(data);
                data = JSON.parse(data);
                if(data.status == "ok"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Chamado enviado com sucesso',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    window.location.href = url + "/_/admin/corridas.php";
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao enviar chamado',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        });
    }


    function getTaxa() {
        let endereco_ini = $('#endereco_ini').val();
        let endereco_fim = $('#endereco_fim').val();
        $.ajax({
            url: url + "/_/funcoes/get_taxa.php",
            type: 'post',
            data: {
                endereco_ini: endereco_ini,
                endereco_fim: endereco_fim,
                lat_ini: lat_ini,
                lng_ini: lng_ini,
                lat_fim: lat_fim,
                lng_fim: lng_fim,
                categoria_id: categoria_id,
                cidade_id: cidade_id
            },
            success: function(data) {
                console.log(data);
                data = JSON.parse(data);
                $('#total').html(data.taxa);
                $('#tempo').html(data.minutos);
                $('#km').html(data.km);
                $('#btn-enviar').removeAttr('disabled');
                km = data.km;
                tempo = data.minutos;
                taxa = data.taxa;
                //colocar borda verde no card-estimativas
                $('#card-estimativas').css('border', '1px solid green');
            }
        });
    }
</script>

</html>