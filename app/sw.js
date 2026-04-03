const CACHE_NAME = "v1";

    // Lista de arquivos a serem armazenados em cache
    const cacheFiles = [
      "assets/icon-152x152.png",
      "assets/icon-167x167.png",
      "assets/icon-192x192.png",
      "assets/icon-512x512.png",
      "index.php",
        "index.js",
        "login.php",
        "login.js",
        "cadastro.php",
        "cadastro.js",
        "home.php",
        "home.js",
        "carteira.php",
        "carteira.js",
        "mensagens.php",
        "mensagens.js",
        "historico.php",
        "historico.js",
        
    ];
    
    // Instale o Service Worker
    self.addEventListener("install", function(event) {
      event.waitUntil(
        caches.open(CACHE_NAME)
          .then(function(cache) {
            return cache.addAll(cacheFiles);
          })
      );
    });
    
    // Gerencie solicitações de recursos
    self.addEventListener("fetch", (event) => {
        if (event.request.method === "GET") {
          event.respondWith(
            fetch(event.request)
              .then((response) => {
                const clonedResponse = response.clone();
      
                caches.open("version-1").then((cache) => {
                  cache.put(event.request, clonedResponse);
                });
      
                return response;
              })
              .catch(() => {
                return caches.match(event.request);
              })
          );
        } else {
          event.respondWith(fetch(event.request));
        }
      });
    
    // Atualize o cache quando um novo Service Worker for ativado
    self.addEventListener("activate", function(event) {
      event.waitUntil(
        caches.keys().then(function(cacheNames) {
          return Promise.all(
            cacheNames.map(function(thisCacheName) {
              if (thisCacheName !== CACHE_NAME) {
                return caches.delete(thisCacheName);
              }
            })
          );
        })
      );
    });