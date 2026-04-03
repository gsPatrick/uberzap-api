<!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="theme-color" content="#ffffff">
            <title>Ubezap</title>
            <!-- bootstrap css -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <!-- bootstrap icons -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
            <!-- sweetalert -->
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!--material icons-->
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            <style>
            .floating-card {
              position: fixed;
              bottom: 5px;
              left: 50%;
              transform: translate(-50%, 50%);
              background-color: white;
              box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
              padding: 20px;
              border-radius: 10px;
              animation: float-up 0.5s ease-out;
              animation-fill-mode: forwards;
              width: 80%;
              max-width: 400px;
              z-index: 2000;
            }
            @keyframes float-up {
              from {
                transform: translate(-50%, 100%);
              }
              to {
                transform: translate(-50%, 0);
              }
            }
          </style>
            <link rel="manifest" href="manifest.json">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-title" content="Ubezap">
            <meta name="apple-mobile-web-app-status-bar-style" content="default">
            <meta name="msapplication-starturl" content="index.php">
            <link rel="icon" sizes="192x192" href="assets/icon-192x192.png">
            <link rel="apple-touch-icon" href="assets/icon-192x192.png">
            <link rel="shortcut icon" href="assets/icon-192x192.png">
            </head>
        <body>
        <div id="loading-page-bb" style="opacity: 0; height: 100%;">
              <div id="splash" class="classe_da_tela" style="background-color: #333333; height: 100px; width: 100%;">
    <img src="assets/logo-Uberzap.png" height="50px" width="50px" id="img_logo">
  </div>
<div class="floating-card" id="install-card" style="display: none;">
             <div class="container">
                <p>Deseja instalar o aplicativo?</p>
                     <div class="row justify-content-around">
                         <div class="col">
                             <button class="btn btn-success" id="install-button-pwa">Instalar</button>
                         </div>
                         <div class="col">
                             <button id="reject-button" class="btn btn-light">Recusar</button>
                        </div>
                 </div>
             </div>
        </div><script>
        let deferredPrompt;

        if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker.register("sw.js").then(function(registration) {
            console.log("ServiceWorker registration successful with scope: ", registration.scope);
            }, function(err) {
            console.log("ServiceWorker registration failed: ", err);
            });
        });
        }

        window.addEventListener("beforeinstallprompt", function(e) {
            console.log("beforeinstallprompt Event fired");
            e.preventDefault();
            try {
              deferredPrompt = e;
              document.querySelector(".floating-card").style.display = "block";
              if (typeof notInstalledFunction === "function") {
                notInstalledFunction();
              }
            } catch (error) {
              console.error(error);
            }
        });

        document.querySelector("#install-button-pwa").addEventListener("click", function() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult) {
            console.log(choiceResult.outcome);
            if (choiceResult.outcome === "dismissed") {
                console.log("User cancelled home screen install");
                if (typeof rejectFunction === "function") {
                    rejectFunction();
                  }
            } else {
                console.log("User added to home screen");
                if (typeof installFunction === "function") {
                    installFunction();
                  }
            }
            deferredPrompt = null;
            });
        }else{
            console.log("Não foi possível instalar o aplicativo");
            console.log(deferredPrompt);
        }
        });
        const rejectButton = document.querySelector("#reject-button");
        rejectButton.addEventListener("click", () => {
        const card = document.querySelector("#install-card");
        card.style.display = "none";

        if (typeof rejectFunction === "function") {
            rejectFunction();
        }
        });

        </script>
            <!-- bootstrap js -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
            <!-- jquery -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.js" integrity="sha512-CX7sDOp7UTAq+i1FYIlf9Uo27x4os+kGeoT7rgwvY+4dmjqV0IuE/Bl5hVsjnQPQiTOhAX1O2r2j5bjsFBvv/A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <!-- firebase-app -->
            <script src="https://www.gstatic.com/firebasejs/7.21.0/firebase-app.js"></script>
            <!-- firebase-database -->
            <script src="https://www.gstatic.com/firebasejs/7.21.0/firebase-database.js"></script>
            <!-- firebase-auth -->
            <script src="https://www.gstatic.com/firebasejs/7.15.5/firebase-auth.js"></script>
            <!-- codigo javascript -->
            <script src= "index.js?v=1.0"> </script>
        </div>
        </body>
        </html>