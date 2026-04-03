<style>
/* The device with borders */
.isMoving {
   z-index: 1001 !important;
}
.smartphone {
    z-index: 1000;
    display: block;
  position: absolute;
  width: 321px;
  height: 530px;
  left: 10%;
  border: 16px black solid;
  border-top-width: 60px;
  border-bottom-width: 60px;
  border-radius: 36px;
}

/* The horizontal line on the top of the device */
.smartphone:before {
  content: '';
  display: block;
  width: 60px;
  height: 5px;
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #333;
  border-radius: 10px;
}

/* The circle on the bottom of the device */
.smartphone:after {
  content: '';
  display: block;
  width: 35px;
  height: 35px;
  position: absolute;
  left: 50%;
  bottom: -65px;
  transform: translate(-50%, -50%);
  background: #333;
  border-radius: 50%;
}

/* The screen (or content) of the device */
.smartphone .content {
  width: 320px;
  height: 530px;
  background: white;
}

body{
  /* backgrond image from img/fundo.jpg */
  background-image: url("img/fundo.png");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
}

</style>
<?php
include_once("functions/mobile_detect.php");


if(isset( $_SERVER["REQUEST_URI"])&& $_SERVER["REQUEST_URI"] !=""){
 $parametros = explode("?",$_SERVER["REQUEST_URI"]);
 if(isset($parametros[1])&&$parametros[1]!=""){
  $parametros_string = $parametros[1];
 }else{
  $parametros_string = "";
 }
}

if(isset($_GET['pag'])){
  $pag = $_GET['pag'];
  //verifica se conten .php
  if (strpos($pag, '.php') !== false) {
    $pag = str_replace(".php","",$pag);
  }
}else{
  $pag = "index";
}
// pega host e junta com /app
$base_url = "https://".$_SERVER['HTTP_HOST']."/app/".$pag.".php";
if($parametros_string!=""){
  $base_url = $base_url."?".$parametros_string;
}

if(isMobile()){ ?>
<script>
    var url_base = "<?php echo $base_url; ?>";
    window.location.href = url_base;
</script>
<?php
}else{
?>
<script>
    function iframe_onload(){};
</script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title;?></title>
</head>
<body>
<div class="smartphone" id="device_smart" >
  <div id="div_device" class="content">
  <iframe src="<?php  echo $base_url; ?>" id="iframe_device_smart" onload="iframe_onload()"  style="width:100%;border:none;height:100%;" />
  </div>
</div>
</body>
</html>


<?php }
?>