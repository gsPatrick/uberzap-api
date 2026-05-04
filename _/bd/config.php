<?php 
// CORS - Libera para todas as origens (deve ser o primeiro include)
include_once __DIR__ . '/cors.php';
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('America/cuiaba'); //mude para o da sua regiao
define ('DOMINIO','https://top.uberzap.app.br'); //mude para o seu dominio
define ('APP_NAME','Uberzap'); //mude para o nome do seu app
define ('KEY_GOOGLE_MAPS', 'AIzaSyALug8RJH4w49LYiJWzQ3Y7iOBf1k5VbWY'); //mudar para chave de api da sua conta
define ('MAPBOX_KEY', 'pk.eyJ1IjoidWJlemFwLW1vYmlsaWRhZGUtdXJiYW5hIiwiYSI6ImNtaGYxM3d3NDAwZ2gycnNjeG9qczU4bmoifQ.2VcBJf-QkhB2Ky9Sm8_B7w'); 
//mudar para chave de api do cliente

define ('URL', DOMINIO . '/_/app/');
define ('URL_IMAGEM', DOMINIO . '/_/admin/uploads/');
define('API_KEY_SMS', '4A4SC41ZDWK604KDAM5P1YRBZWSV02FBAL541RVBQ374NRUMCMOY162ETATC7GAOIQA1WFO1PDTPPMXCIHU19HP4WZG885Z9TA');
//altere para a sua em https://smsdev.com.br


//credenciais para bot do whatsapp
define('PATCH_LIMPA_MSG', '316e783e-e696-4f42-87e8-6b9411a58796');
define('W_API_ID', 'LITE-EP5ZVE-E8IU0I');
define('W_API_TOKEN', 'fIBtAA1oNdQR8K92en2dZVhG6umRuAIvl');