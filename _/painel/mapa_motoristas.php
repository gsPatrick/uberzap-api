<?php
include("seguranca.php");
include_once("../bd/config.php");      // <--- CORRIGIDO
include_once("../classes/motoristas.php"); // <--- CORRIGIDO
include_once("../classes/cidades.php"); 
$e = new motoristas();
$c = new cidades();
$dados_cidade = $c->get_dados_cidade($cidade_id);
$motoristas =  array();
$motoristas = $e->get_motoristas($cidade_id, true);
//var_dump($motoristas);
// if (!empty($motoristas)) {
//     if ($motoristas[0]['lat'] == 0 || $motoristas[0]['lng'] == 0) {
//         $lat_ini = $dados_cidade['latitude'];
//         $lng_ini = $dados_cidade['longitude'];
//     } else {
//         $lat_ini = $motoristas[0]['lat'];
//         $lng_ini = $motoristas[0]['lng'];
//     }
// } else {
    $lat_ini = $dados_cidade['latitude'];
    $lng_ini = $dados_cidade['longitude'];
// }
//busca todos os motoristas

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
</body>

</html>
<?php include("dep_query.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
<script>
    let motoristas = <?php echo json_encode($motoristas); ?>;
    let url = '<?php echo DOMINIO; ?>';
    let url_base = url + "/_/painel/assets/img/"; // <--- ESTA LINHA FOI ALTERADA (se você copiou as imagens para lá)
    let lat_ini = <?php echo $lat_ini; ?>;
    let lng_ini = <?php echo $lng_ini; ?>;
    var map;
    var markers = [];

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: lat_ini,
                lng: lng_ini
            },
            zoom: 13.5,
        });
        listar_motoristas(motoristas);
    }

    function listar_motoristas(motoristas) {
        motoristas.forEach(function(entregador) {
            if (entregador.latitude == 0 || entregador.longitude == 0) return;
            //convert to number
            entregador.latitude = +entregador.latitude;
            entregador.longitude = +entregador.longitude;

            var myLatLng = {
                lat: entregador.latitude,
                lng: entregador.longitude
            };
            var iconUrl = entregador.online == 1 ? "mot_on_2.png" : "mot_off_2.png";
            var marker = new google.maps.Marker({


                position: myLatLng,
                map: map,
                title: entregador.nome,
                icon: {
                    url: url_base + iconUrl,
                    scaledSize: new google.maps.Size(40, 40),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(20, 40),
                },
            });
            markers.push(marker);
            //quando clica no marcador exibe o nome do entregador
            marker.addListener("click", () => {
                var infowindow = new google.maps.InfoWindow({
                    content: entregador.nome,
                });
                infowindow.open(map, marker);
            });
        });
    }

    function excluir_marcadores() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
    }

    setInterval(function() {
        $.ajax({
            url: url + "/_/painel/funcoes/get_motoristas.php", // <--- ESTA LINHA FOI ALTERADA
            type: "POST",
            data: {
                cidade_id: <?php echo $cidade_id; ?>
            },
            dataType: "json",
            success: function(data) {
                motoristas = data;
                excluir_marcadores();
                listar_motoristas(motoristas);
            },
            error: function(data) {
                console.log(data);
            },
        });
    }, 10000);
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo KEY_GOOGLE_MAPS; ?>&callback=initMap"></script>
