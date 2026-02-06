/**
 * WPORLogin - Smart Video Background Engine (YouTube ONLY Version)
 *//*
(function() {
    'use strict';

    window.WPORLoginVideo = {
        config: {},
        
        init: function() {
            // Si viene de params (Front) o config manual (Customizer JS)
            if ( typeof wporlogin_video_params !== 'undefined' ) {
                this.config = wporlogin_video_params;
            }

            // Validación básica
            if ( ! this.config.videoUrl ) return;

            // 1. Crear Wrapper si no existe
            if ( ! document.getElementById('wporlogin-video-wrapper') ) {
                this.createWrapper();
            }

            // 2. Smart Check: ¿Es móvil?
            if ( this.isMobile() ) {
                this.loadFallback();
                return; // En móviles mostramos imagen, no cargamos iframe
            }

            // 3. Cargar Video (Inteligente: YouTube o Vimeo)
            // [MODIFICADO] Antes llamaba a loadYouTube, ahora llama al parser general
            this.parseExternalVideo(this.config.videoUrl);
        },

        createWrapper: function() {
            var wrap = document.createElement('div');
            wrap.id = 'wporlogin-video-wrapper';
            // Guardamos la URL actual para comparaciones en el customizer
            wrap.setAttribute('data-url', this.config.videoUrl);
            
            // Estilos CSS: Fixed, detrás de todo, pantalla completa
            wrap.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden; background:#000;';
            document.body.prepend(wrap);

            // Overlay (Capa de color)
            var overlay = document.createElement('div');
            overlay.id = 'wporlogin-video-overlay';
            overlay.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; z-index:2; pointer-events:none;';
            if(this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
            wrap.appendChild(overlay);
        },

        isMobile: function() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;
        },

        loadFallback: function() {
            var wrap = document.getElementById('wporlogin-video-wrapper');
            if (this.config.fallbackUrl) {
                wrap.style.backgroundImage = 'url(' + this.config.fallbackUrl + ')';
                wrap.style.backgroundSize = 'cover';
                wrap.style.backgroundPosition = 'center center';
            }
        },

        // [NUEVA FUNCIÓN] Reemplaza a loadYouTube
        parseExternalVideo: function(url) {
            var videoId = null;
            var wrap = document.getElementById('wporlogin-video-wrapper');
            var iframe = document.createElement('iframe');

            // Estilos CSS para simular "object-fit: cover" en iframe
            iframe.style.cssText = 'position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:100vw; height:56.25vw; min-height:100vh; min-width:177.77vh; z-index:1; pointer-events:none;';
            iframe.allow = "autoplay; fullscreen";
            iframe.frameBorder = 0;

            // --- LÓGICA YOUTUBE ---
            if ( url.includes('youtube') || url.includes('youtu.be') ) {
                var match = url.match(/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=))([\w\-]{10,12})\b/);
                if (match && match[1]) {
                    videoId = match[1];
                    // Params: mute=1 (vital), playlist=ID (vital para loop)
                    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?controls=0&showinfo=0&rel=0&autoplay=1&loop=1&mute=1&playlist=' + videoId;
                    wrap.appendChild(iframe);
                }
            } 
            
            // --- LÓGICA VIMEO ---
            else if ( url.includes('vimeo') ) {
                var match = url.match(/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/);
                if (match && match[1]) {
                    videoId = match[1];
                    // Params: background=1 (Hace magia: quita controles, mutea y hace loop automáticamente)
                    iframe.src = 'https://player.vimeo.com/video/' + videoId + '?background=1&autoplay=1&loop=1&byline=0&title=0';
                    wrap.appendChild(iframe);
                }
            }
        }
    };

    // Inicializar al cargar el DOM
    document.addEventListener('DOMContentLoaded', function() {
        window.WPORLoginVideo.init();
    });

})();*/
/**
 * WPORLogin - Smart Video Background Engine (Universal: Local & External)
 *//*
(function() {
    'use strict';

    window.WPORLoginVideo = {
        config: {},
        
        init: function() {
            // 1. Recibir datos de PHP (SOLO si no hay configuración manual previa)
            // CORRECCIÓN: Verificamos si this.config ya tiene datos (inyectados por el Customizer).
            // Si está vacío, cargamos los defaults de PHP. Si no, respetamos lo que mandó el JS.
            if ( typeof wporlogin_video_params !== 'undefined' && !this.config.videoUrl ) {
                this.config = wporlogin_video_params;
            }

            // Validación
            if ( ! this.config.videoUrl ) return;

            // 2. Crear Contenedor (Wrapper) si no existe
            if ( ! document.getElementById('wporlogin-video-wrapper') ) {
                this.createWrapper();
            }

            // 3. Móvil: Fallback a imagen
            if ( this.isMobile() ) {
                this.loadFallback();
                return; 
            }

            // 4. DECISIÓN DE RENDERIZADO
            // Aquí el JS inserta dinámicamente según el tipo
            if ( this.config.type === 'local' ) {
                this.parseLocalVideo(this.config.videoUrl);
            } else {
                this.parseExternalVideo(this.config.videoUrl);
            }
        },

        createWrapper: function() {
            var wrap = document.createElement('div');
            wrap.id = 'wporlogin-video-wrapper';
            wrap.setAttribute('data-url', this.config.videoUrl); // Meta-dato útil
            
            // CSS para fondo fijo detrás de todo
            wrap.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden; background:#000;';
            document.body.prepend(wrap);

            // Crear Overlay
            var overlay = document.createElement('div');
            overlay.id = 'wporlogin-video-overlay';
            overlay.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; z-index:2; pointer-events:none;';
            if(this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
            wrap.appendChild(overlay);
        },

        isMobile: function() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;
        },

        loadFallback: function() {
            var wrap = document.getElementById('wporlogin-video-wrapper');
            if (this.config.fallbackUrl) {
                wrap.style.backgroundImage = 'url(' + this.config.fallbackUrl + ')';
                wrap.style.backgroundSize = 'cover';
                wrap.style.backgroundPosition = 'center center';
            }
        },

        // --- INSERTAR VIDEO LOCAL (MP4) ---
        parseLocalVideo: function(url) {
            var wrap = document.getElementById('wporlogin-video-wrapper');
            
            // [LIMPIEZA CRUCIAL] Borrar iframes externos si existen
            var iframe = wrap.querySelector('iframe');
            if (iframe) iframe.remove();
            
            // Si ya existe un video con la misma URL, no hacer nada (evita parpadeo)
            var currentVideo = wrap.querySelector('video source');
            if (currentVideo && currentVideo.src === url) {
                // Solo actualizar overlay si hace falta
                var overlay = document.getElementById('wporlogin-video-overlay');
                if(overlay && this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
                return;
            }

            // Si es URL distinta o no hay video, limpiar y crear
            // Mantener overlay
            var overlayDiv = document.getElementById('wporlogin-video-overlay');
            wrap.innerHTML = ''; 
            if (overlayDiv) wrap.appendChild(overlayDiv);

            // Crear etiqueta <video>
            var video = document.createElement('video');
            video.autoplay = true;
            video.loop = true;
            video.muted = true;
            video.playsInline = true;
            
            // Estilos CSS
            video.style.cssText = 'object-fit:cover; width:100%; height:100%; position:absolute; top:0; left:0; z-index:1;';
            
            var source = document.createElement('source');
            source.src = url;
            source.type = 'video/mp4';
            
            video.appendChild(source);
            wrap.appendChild(video);
        },

        // --- INSERTAR VIDEO EXTERNO (IFRAME) ---
        parseExternalVideo: function(url) {
            var videoId = null;
            var wrap = document.getElementById('wporlogin-video-wrapper');
            
            // [LIMPIEZA CRUCIAL] Borrar video local si existe
            var oldVideo = wrap.querySelector('video');
            if (oldVideo) oldVideo.remove();

            // Evitar recargar el iframe si la URL es la misma (para postMessage de colores)
            var oldIframe = wrap.querySelector('iframe');
            if (oldIframe && wrap.getAttribute('data-active-url') === url) {
                 var overlay = document.getElementById('wporlogin-video-overlay');
                 if(overlay && this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
                 return;
            }
            if (oldIframe) oldIframe.remove();

            wrap.setAttribute('data-active-url', url);

            var iframe = document.createElement('iframe');
            iframe.style.cssText = 'position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:100vw; height:56.25vw; min-height:100vh; min-width:177.77vh; z-index:1; pointer-events:none;';
            iframe.allow = "autoplay; fullscreen";
            iframe.frameBorder = 0;

            if ( url.includes('youtube') || url.includes('youtu.be') ) {
                var match = url.match(/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=))([\w\-]{10,12})\b/);
                if (match && match[1]) {
                    videoId = match[1];
                    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?controls=0&showinfo=0&rel=0&autoplay=1&loop=1&mute=1&playlist=' + videoId;
                    wrap.appendChild(iframe);
                }
            } else if ( url.includes('vimeo') ) {
                var match = url.match(/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/);
                if (match && match[1]) {
                    videoId = match[1];
                    iframe.src = 'https://player.vimeo.com/video/' + videoId + '?background=1&autoplay=1&loop=1&byline=0&title=0';
                    wrap.appendChild(iframe);
                }
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.WPORLoginVideo.init();
    });

})();*/
/**
 * WPORLogin - Smart Video Background Engine (High Performance)
 * OPTIMIZED: Implementa "Poster Image" para velocidad percibida instantánea.
 */
(function() {
    'use strict';

    window.WPORLoginVideo = {
        config: {},
        
        init: function() {
            // 1. Configuración: Prioridad a datos inyectados por JS (Customizer), luego PHP.
            if ( typeof wporlogin_video_params !== 'undefined' && !this.config.videoUrl ) {
                this.config = wporlogin_video_params;
            }

            if ( ! this.config.videoUrl ) return;

            // 2. Crear Wrapper (Ahora con imagen de fondo inmediata)
            if ( ! document.getElementById('wporlogin-video-wrapper') ) {
                this.createWrapper();
            }

            // 3. Móvil: Detenemos aquí (mostramos solo la imagen cargada en createWrapper)
            if ( this.isMobile() ) {
                return; 
            }

            // 4. Inserción del Video
            if ( this.config.type === 'local' ) {
                this.parseLocalVideo(this.config.videoUrl);
            } else {
                this.parseExternalVideo(this.config.videoUrl);
            }
        },

        createWrapper: function() {
            var wrap = document.createElement('div');
            wrap.id = 'wporlogin-video-wrapper';
            wrap.setAttribute('data-url', this.config.videoUrl);
            
            // TRUCO EXPERTO: Usamos la imagen fallback como fondo del wrapper.
            // Esto se muestra INSTANTÁNEAMENTE mientras el video carga encima.
            var bgStyle = 'background-color:#000000;';
            if ( this.config.fallbackUrl ) {
                bgStyle += 'background-image:url(' + this.config.fallbackUrl + '); background-size:cover; background-position:center center;';
            }

            wrap.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden;' + bgStyle;
            document.body.prepend(wrap);

            // Overlay (Capa de color)
            var overlay = document.createElement('div');
            overlay.id = 'wporlogin-video-overlay';
            // Z-Index 2 asegura que esté encima del video (Z-Index 1) y del fondo (Z-Index 0)
            overlay.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; z-index:2; pointer-events:none; transition: background-color 0.2s ease;';
            if(this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
            wrap.appendChild(overlay);
        },

        isMobile: function() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;
        },

        // --- INSERTAR VIDEO LOCAL (MP4) ---
        parseLocalVideo: function(url) {
            var wrap = document.getElementById('wporlogin-video-wrapper');
            
            // Limpieza inteligente
            var iframe = wrap.querySelector('iframe');
            if (iframe) iframe.remove();
            
            var currentVideo = wrap.querySelector('video source');
            if (currentVideo && currentVideo.src === url) {
                // Si es el mismo video, solo actualizamos overlay y salimos
                var overlay = document.getElementById('wporlogin-video-overlay');
                if(overlay && this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
                return;
            }

            // Limpiar contenido previo (manteniendo overlay si existe, aunque aquí regeneramos)
            // Nota: Al limpiar innerHTML borramos el overlay, hay que restaurarlo o no borrarlo.
            // Mejor estrategia: Borrar solo video anterior.
            var oldVideo = wrap.querySelector('video');
            if (oldVideo) oldVideo.remove();

            // Crear Video
            var video = document.createElement('video');
            video.autoplay = true;
            video.loop = true;
            video.muted = true;
            video.playsInline = true;
            
            // OPTIMIZACIÓN DE CARGA
            video.preload = 'auto'; // Indica al navegador que descargue con prioridad alta
            
            // Estilos: Z-index 1 para tapar la imagen de fondo (z-index 0) pero estar bajo el overlay (z-index 2)
            // Opacity 0 inicial para evitar parpadeo de cuadro negro, luego fade-in
            video.style.cssText = 'object-fit:cover; width:100%; height:100%; position:absolute; top:0; left:0; z-index:1; opacity:0; transition: opacity 0.5s ease;';
            
            var source = document.createElement('source');
            source.src = url;
            source.type = 'video/mp4';
            
            video.appendChild(source);
            
            // Evento Mágico: Cuando el video tenga suficientes datos para reproducir, muéstralo.
            video.oncanplay = function() {
                video.style.opacity = '1';
            };

            // Insertamos ANTES del overlay para respetar capas
            var overlayDiv = document.getElementById('wporlogin-video-overlay');
            if (overlayDiv) {
                wrap.insertBefore(video, overlayDiv);
            } else {
                wrap.appendChild(video);
            }
        },

        // --- INSERTAR VIDEO EXTERNO (IFRAME) ---
        parseExternalVideo: function(url) {
            var videoId = null;
            var wrap = document.getElementById('wporlogin-video-wrapper');
            
            var oldVideo = wrap.querySelector('video');
            if (oldVideo) oldVideo.remove();

            // Check if same URL to avoid reload
            if (wrap.getAttribute('data-active-url') === url && wrap.querySelector('iframe')) {
                 var overlay = document.getElementById('wporlogin-video-overlay');
                 if(overlay && this.config.overlayColor) overlay.style.backgroundColor = this.config.overlayColor;
                 return;
            }
            
            var oldIframe = wrap.querySelector('iframe');
            if (oldIframe) oldIframe.remove();

            wrap.setAttribute('data-active-url', url);

            var iframe = document.createElement('iframe');
            // Opacity 0 al inicio para que no se vea el cuadro de carga de YouTube
            iframe.style.cssText = 'position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:100vw; height:56.25vw; min-height:100vh; min-width:177.77vh; z-index:1; pointer-events:none; opacity:0; transition: opacity 0.8s ease;';
            iframe.allow = "autoplay; fullscreen";
            iframe.frameBorder = 0;

            // Evento: Cuando el iframe carga, lo mostramos
            iframe.onload = function() {
                iframe.style.opacity = '1';
            };

            if ( url.includes('youtube') || url.includes('youtu.be') ) {
                var match = url.match(/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=))([\w\-]{10,12})\b/);
                if (match && match[1]) {
                    videoId = match[1];
                    // Agregamos loading=eager para prioridad
                    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?controls=0&showinfo=0&rel=0&autoplay=1&loop=1&mute=1&playlist=' + videoId;
                }
            } else if ( url.includes('vimeo') ) {
                var match = url.match(/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/);
                if (match && match[1]) {
                    videoId = match[1];
                    iframe.src = 'https://player.vimeo.com/video/' + videoId + '?background=1&autoplay=1&loop=1&byline=0&title=0';
                }
            }

            if (videoId) {
                var overlayDiv = document.getElementById('wporlogin-video-overlay');
                if (overlayDiv) {
                    wrap.insertBefore(iframe, overlayDiv);
                } else {
                    wrap.appendChild(iframe);
                }
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.WPORLoginVideo.init();
    });

})();