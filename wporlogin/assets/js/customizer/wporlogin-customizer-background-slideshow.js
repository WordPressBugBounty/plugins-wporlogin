/**
 * WPORLogin - Slideshow (Random Backgrounds) - Motor Reactivo V2
 * Expone métodos globales para actualizar velocidad Y color en tiempo real.
 */
(function() {
    'use strict';

    window.WPORLoginSlideshow = {
        interval: null,
        config: {},
        images: [],
        currentIndex: 0,

        // Función para barajar array (Fisher-Yates Shuffle)
        shuffleArray: function(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        },
        
        init: function() {
            if ( typeof wporlogin_slideshow_params === 'undefined' ) return;
            this.config = wporlogin_slideshow_params;

            // Obtenemos las imágenes
            var rawImages = this.config.images || [];
            
            // [NUEVO] ¡AQUÍ ES DONDE LAS HACEMOS ALEATORIAS!
            // Barajamos el array antes de guardarlo en this.images
            this.images = this.shuffleArray(rawImages);
            
            if ( this.images.length === 0 ) return;

            // Crear contenedor si no existe
            if ( ! document.getElementById('wporlogin-slideshow-wrapper') ) {
                this.createWrapper();
            }

            this.startLoop();
        },

        createWrapper: function() {
            // 1. Contenedor Principal
            var bgContainer = document.createElement('div');
            bgContainer.id = 'wporlogin-slideshow-wrapper';
            bgContainer.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden; background-color:#000;';
            document.body.prepend(bgContainer);
            
            // 2. [NUEVO] Capa de Overlay Independiente
            // La creamos una sola vez y la dejamos fija encima de las fotos
            var overlayDiv = document.createElement('div');
            overlayDiv.id = 'wporlogin-slideshow-overlay';
            // Obtenemos el color inicial o usamos negro transparente por defecto
            var initialColor = (this.config.overlayRgba) ? this.config.overlayRgba : 'rgba(0,0,0,0.5)';
            
            overlayDiv.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; z-index:10; pointer-events:none; transition: background-color 0.2s ease;';
            overlayDiv.style.backgroundColor = initialColor;
            
            bgContainer.appendChild(overlayDiv);

            // Mostrar primera imagen
            this.showImage(this.images[0]);
        },

        showImage: function(imgUrl) {
            var bgContainer = document.getElementById('wporlogin-slideshow-wrapper');
            if (!bgContainer) return;

            var slide = document.createElement('div');
            
            // [CAMBIO] Ya no ponemos el gradiente aquí, solo la imagen.
            // El color ahora lo maneja la capa 'wporlogin-slideshow-overlay'
            slide.style.cssText = 'position:absolute; top:0; left:0; width:100%; height:100%; background-size:cover; background-position:center center; opacity:0; transition: opacity 1.5s ease-in-out; z-index:1;';
            slide.style.backgroundImage = 'url(' + imgUrl + ')';

            // Insertamos la diapositiva
            bgContainer.appendChild(slide);
            
            // [TRUCO] Aseguramos que el overlay siempre esté AL FINAL (encima de todo)
            var overlayDiv = document.getElementById('wporlogin-slideshow-overlay');
            if(overlayDiv) {
                bgContainer.appendChild(overlayDiv); 
            }

            void slide.offsetWidth; // Reflow
            slide.style.opacity = '1';

            setTimeout(function() {
                // Borramos la imagen vieja (pero no borramos el overlay)
                // Filtramos para borrar solo elementos que sean diapositivas viejas
                // Como el overlay siempre lo movemos al final, los hijos al principio son slides viejos.
                while (bgContainer.children.length > 2) { 
                    // Mantenemos: 1 slide nuevo + 1 overlay = 2 hijos mínimos. 
                    // Si hay más, borramos el primero (el más viejo).
                    if (bgContainer.firstChild.id !== 'wporlogin-slideshow-overlay') {
                        bgContainer.removeChild(bgContainer.firstChild);
                    } else {
                        // Si por error el primero es el overlay, no lo borramos (seguridad)
                        break;
                    }
                }
            }, 1600);
        },

        startLoop: function() {
            if ( this.interval ) clearInterval(this.interval);

            if ( this.images.length > 1 ) {
                var durationMs = parseInt(this.config.duration || 5) * 1000;
                var self = this;
                
                this.interval = setInterval(function() {
                    self.currentIndex = (self.currentIndex + 1) % self.images.length;
                    
                    var img = new Image();
                    img.src = self.images[self.currentIndex];
                    img.onload = function() {
                        self.showImage(self.images[self.currentIndex]);
                    };
                }, durationMs);
            }
        },

        // --- MÉTODOS PÚBLICOS PARA EL CUSTOMIZER ---

        updateDuration: function( newSeconds ) {
            this.config.duration = newSeconds;
            this.startLoop();
        },

        // [NUEVO] Actualizar color en tiempo real
        updateOverlay: function( newRgba ) {
            var overlayDiv = document.getElementById('wporlogin-slideshow-overlay');
            if ( overlayDiv ) {
                overlayDiv.style.backgroundColor = newRgba;
            }
            // Actualizamos la config interna también
            this.config.overlayRgba = newRgba;
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        window.WPORLoginSlideshow.init();
    });

})();