<?php
include("seguranca.php");
include_once("../classes/dinamico_mapa.php");
include_once("../classes/cidades.php");
include_once("../bd/config.php");
$c = new cidades();
$dm = new dinamico_mapa();
$dados_cidade = $c->get_dados_cidade($cidade_id);

$lat_ini = $dados_cidade['latitude'];
$lng_ini = $dados_cidade['longitude'];

//busca dinamicos cadastrados
$dados = $dm->get_dinamico_mapa($cidade_id);

?>
<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<?php include("menu.php"); ?>
<style>
    #map {
        height: 100%;
    }

    /* Optional: Makes the sample page fill the window. */
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
</style>

<body>
    <div id="map" style="width: 100%; height: 88%"></div>
    <!-- Modal -->
    <div class="modal fade" id="modal-dados-dinamico" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Dados do Dinamico</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <label for="d_nome">Nome</label>
                            <input type="text" class="form-control" id="d_nome" placeholder="ex: bairro sao vicente">
                        </div>
                        <div class="row">
                            <label for="d_raio">Raio em Metros</label>
                            <input type="text" class="form-control" id="d_raio" placeholder="ex: 500">
                        </div>
                        <div class="row">
                            <label for="d_adicional">Valor Adicional</label>
                            <input type="text" class="form-control" id="d_adicional" placeholder="ex: 1,50">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" id="save_dinamico" class="btn btn-primary">Salvar dinâmico</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- modal -->
</body>

</html>
<?php include("dep_query.php"); ?>


<script>
    let dinamicos = <?php echo json_encode($dados); ?>;
    let url = '<?php echo DOMINIO; ?>';
    let url_base = url + "/_/assets/img/";
    let lat_ini = <?php echo $lat_ini; ?>;
    let lng_ini = <?php echo $lng_ini; ?>;
    let lat_novo_dinamico = 0;
    let lng_novo_dinamico = 0;
    var map;
    var markers = [];

    function excluir_dinamico(id) {
            $.ajax({
                url: url + "/_/funcoes/excluir_dinamico.php",
                type: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    if (data == "ok") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dinâmico excluído com sucesso',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        //recarrega a pagina depois de 1.5 segundos
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert("Erro ao excluir dinâmico");
                        console.log(data);
                    }
                },
            });
        }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: lat_ini,
                lng: lng_ini
            },
            zoom: 13.5,
        });

        map.addListener("click", function(event) {
            adicionarDinamico(event.latLng.lat(), event.latLng.lng());
        });
        listar_dinamicos(dinamicos);
    }

    function listar_dinamicos(dinamicos) {
        console.log(dinamicos);
        //verifica se existe algo na lista
        if (dinamicos.length == 0) return;
        dinamicos.forEach(function(dinamico) {
            if (dinamico.latitude == 0 || dinamico.longitude == 0) return;
            //convert to number
            dinamico.latitude = +dinamico.latitude;
            dinamico.longitude = +dinamico.longitude;

            var myLatLng = {
                lat: dinamico.latitude,
                lng: dinamico.longitude
            };
            var iconUrl = "money.png";
            var marker = new google.maps.Marker({


                position: myLatLng,
                map: map,
                title: dinamico.nome,
                icon: {
                    url: url_base + iconUrl,
                    scaledSize: new google.maps.Size(30, 30),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(15, 15),
                },
            });

            markers.push(marker);
            //quando clica no marcador exibe o nome do dinamico
            marker.addListener("click", () => {
                var infowindow = new google.maps.InfoWindow({
                    content: dinamico.nome + "<br> Raio: " + dinamico.raio + " metros <br> Adicional: R$ " + dinamico.adicional +"<br><br><button class='btn btn-danger btn-sm' onclick='excluir_dinamico("+dinamico.id+")'><i class='bi bi-trash'></i></button>",
                });
                infowindow.open(map, marker);
            });

            // Add a circle based on the radius
            let raio = +dinamico.raio;
            var circle = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                map: map,
                center: myLatLng,
                radius: raio
            });

        });
    }

    function adicionarDinamico(lat, lng) {
        // abre um modal para colocar nome, raio e valor adicional
        $("#modal-dados-dinamico").modal("show");
        lat_novo_dinamico = lat;
        lng_novo_dinamico = lng;
    }

    function excluir_marcadores() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
    }

    $("#save_dinamico").click(function() {
        var nome = $("#d_nome").val();
        var raio = $("#d_raio").val();
        var adicional = $("#d_adicional").val();
        let lat = lat_novo_dinamico;
        let lng = lng_novo_dinamico;
        if (nome == "" || raio == "" || adicional == "") {
            alert("Preencha todos os campos");
            return;
        }
        //verifica se existe algo na lista
        if (dinamicos.length == 0) {
            //se nao existir nada na lista, cadastra o primeiro
            cadastra_dinamico(nome, raio, adicional, lat, lng);
            return;
        }
        //verifica se existe algum dinamico com o mesmo nome
        var existe = false;
        dinamicos.forEach(function(dinamico) {
            if (dinamico.nome == nome) {
                existe = true;
            }
        });
        if (existe) {
            alert("Já existe um dinâmico com esse nome");
            return;
        }
        //se nao existir nenhum dinamico com o mesmo nome, cadastra
        cadastra_dinamico(nome, raio, adicional, lat, lng);
    });


    function cadastra_dinamico(nome, raio, adicional, lat, lng) {
        $.ajax({
            url: url + "/_/funcoes/cadastra_dinamico.php",
            type: "POST",
            data: {
                cidade_id: <?php echo $cidade_id; ?>,
                nome: nome,
                raio: raio,
                adicional: adicional,
                lat: lat,
                lng: lng
            },
            success: function(data) {
                if (data == "ok") {
                    Swal.fire({
                            icon: 'success',
                            title: 'Dinâmico cadastrado com sucesso',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    //recarrega a pagina
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert("Erro ao cadastrar dinâmico");
                    console.log(data);
                }
            },
        });

        
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo KEY_GOOGLE_MAPS;?>&callback=initMap"></script>