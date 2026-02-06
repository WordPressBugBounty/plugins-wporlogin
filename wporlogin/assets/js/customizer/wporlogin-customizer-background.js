/**
 * WPORLogin - Customizer JS (Live Preview)
 *//*
(function($) {
    'use strict';

    function hexToRgba(hex, opacity) {
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c = hex.substring(1).split('');
            if(c.length === 3){ c = [c[0], c[0], c[1], c[1], c[2], c[2]]; }
            c = '0x'+c.join('');
            return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+(opacity/100)+')';
        }
        return 'rgba(0,0,0,0)';
    }

    function refreshBackground() {
        var bgColor         = wp.customize('wporlogin_login_background_color').get();
        var overlayColor    = wp.customize('wporlogin_bg_overlay_color').get();
        var overlayOpacity  = wp.customize('wporlogin_bg_overlay_opacity').get();
        var rgba            = hexToRgba(overlayColor, overlayOpacity);
        
        var mode = wp.customize('wporlogin_background_mode') ? wp.customize('wporlogin_background_mode').get() : 'static';

        // 1. LIMPIEZA
        if ( mode !== 'random' ) {
            var $slideshow = $('#wporlogin-slideshow-wrapper');
            if ( $slideshow.length ) $slideshow.remove();
        }
        if ( mode !== 'video' ) {
            var $videoWrap = $('#wporlogin-video-wrapper');
            if ( $videoWrap.length ) $videoWrap.remove();
        }

        // 2. LÓGICA POR MODO

        // --- MODO RANDOM ---
        if ( mode === 'random' ) {
            $('body.login').css({ 'background-color': 'transparent', 'background-image': 'none' });
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateOverlay === 'function' ) {
                window.WPORLoginSlideshow.updateOverlay( rgba );
            }
        } 

        // --- MODO VIDEO (EXTERNAL: YOUTUBE / VIMEO) ---
        else if ( mode === 'video' ) {
            
            $('body.login').css({ 'background-color': 'transparent', 'background-image': 'none' });

            var videoUrl = wp.customize('wporlogin_video_external_url') ? wp.customize('wporlogin_video_external_url').get() : '';
            var $videoContainer = $('#wporlogin-video-wrapper');

            // Si no hay URL, eliminamos contenedor
            if ( ! videoUrl ) {
                if ( $videoContainer.length ) $videoContainer.remove();
                return;
            }

            // Usamos el Engine Global Inteligente (parseExternalVideo)
            if ( window.WPORLoginVideo && typeof window.WPORLoginVideo.parseExternalVideo === 'function' ) {
                
                // Si no existe el contenedor, el init del Engine lo creará
                if ( ! $videoContainer.length ) {
                    window.WPORLoginVideo.config = {
                        type: 'external', // CAMBIO: Usamos 'external' genérico
                        videoUrl: videoUrl,
                        overlayColor: rgba
                    };
                    window.WPORLoginVideo.init();
                } else {
                    // Si ya existe, actualizamos solo si cambió la URL
                    var currentUrl = $videoContainer.attr('data-url');
                    
                    if ( currentUrl !== videoUrl ) {
                        // Reiniciamos el contenedor para cargar el nuevo video
                        $videoContainer.remove();
                        window.WPORLoginVideo.config = {
                            type: 'external',
                            videoUrl: videoUrl,
                            overlayColor: rgba
                        };
                        window.WPORLoginVideo.init();
                    } else {
                        // Solo actualizamos overlay si la URL es la misma
                         $('#wporlogin-video-overlay').css('background-color', rgba);
                    }
                }
            }
        }
        
        // --- MODO STATIC ---
        else if ( mode === 'static' ) {
            var galleryImg = wp.customize('wporlogin_bg_gallery') ? wp.customize('wporlogin_bg_gallery').get() : '';
            var customImg  = wp.customize('wporlogin_login_background_image') ? wp.customize('wporlogin_login_background_image').get() : '';
            var finalImg   = ( customImg && customImg !== '' ) ? customImg : galleryImg;
            
            var bgRepeat   = wp.customize('wporlogin_login_background_repeat') ? wp.customize('wporlogin_login_background_repeat').get() : 'no-repeat';
            var bgSize     = wp.customize('wporlogin_login_background_size') ? wp.customize('wporlogin_login_background_size').get() : 'cover';
            var bgPosition = wp.customize('wporlogin_login_background_position') ? wp.customize('wporlogin_login_background_position').get() : 'center center';

            var bgString = (finalImg) 
                ? 'linear-gradient(' + rgba + ', ' + rgba + '), url(' + finalImg + ')' 
                : 'linear-gradient(' + rgba + ', ' + rgba + ')';

            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString,
                'background-repeat': bgRepeat,
                'background-size': bgSize,
                'background-position': bgPosition,
                'background-attachment': 'fixed'
            });
        } 
        
        // --- MODO DISABLED ---
        else {
            var bgString = (overlayOpacity > 0) ? 'linear-gradient(' + rgba + ', ' + rgba + ')' : 'none';
            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString
            });
        }
    }

    var settings = [
        'wporlogin_background_mode', 
        'wporlogin_login_background_color',
        'wporlogin_bg_gallery', 
        'wporlogin_login_background_image',
        'wporlogin_video_source',     // IMPORTANTE: Escuchar cambio de fuente
        'wporlogin_video_local',      // IMPORTANTE: Escuchar cambio de archivo
        'wporlogin_video_external_url', // Solo escuchamos URL externa
        'wporlogin_bg_overlay_color', 
        'wporlogin_bg_overlay_opacity', 
        'wporlogin_login_background_repeat', 
        'wporlogin_login_background_size', 
        'wporlogin_login_background_position'
    ];

    $.each(settings, function(i, id) {
        wp.customize(id, function(val) { val.bind(refreshBackground); });
    });

    wp.customize( 'wporlogin_random_duration', function( value ) {
        value.bind( function( newval ) {
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateDuration === 'function' ) {
                window.WPORLoginSlideshow.updateDuration( newval );
            }
        } );
    } );

    wp.customize.bind( 'preview-ready', function() { refreshBackground(); } );

})(jQuery);*/
/**
 * WPORLogin - Customizer JS (Live Preview)
 */
/**
 * WPORLogin - Customizer JS (Live Preview)
 */
/**
 * WPORLogin - Customizer JS (Live Preview)
 */
/**
 * WPORLogin - Customizer JS (Live Preview)
 */
/*(function($) {
    'use strict';

    function hexToRgba(hex, opacity) {
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c = hex.substring(1).split('');
            if(c.length === 3){ c = [c[0], c[0], c[1], c[1], c[2], c[2]]; }
            c = '0x'+c.join('');
            return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+(opacity/100)+')';
        }
        return 'rgba(0,0,0,0)';
    }
    
    function refreshBackground() {
        var bgColor         = wp.customize('wporlogin_login_background_color').get();
        var overlayColor    = wp.customize('wporlogin_bg_overlay_color').get();
        var overlayOpacity  = wp.customize('wporlogin_bg_overlay_opacity').get();
        var rgba            = hexToRgba(overlayColor, overlayOpacity);
        
        var mode = wp.customize('wporlogin_background_mode') ? wp.customize('wporlogin_background_mode').get() : 'static';

        // 1. LIMPIEZA GENERAL
        if ( mode !== 'random' ) {
            $('#wporlogin-slideshow-wrapper').remove();
        }

        // 2. LÓGICA POR MODO

        // --- MODO RANDOM ---
        if ( mode === 'random' ) {
            $('#wporlogin-video-wrapper').remove(); // Borrar cualquier video
            $('body.login').css({ 'background-color': 'transparent', 'background-image': 'none' });
            
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateOverlay === 'function' ) {
                window.WPORLoginSlideshow.updateOverlay( rgba );
            }
        } 

        // --- MODO VIDEO (HÍBRIDO) ---
        else if ( mode === 'video' ) {
            
            $('body.login').css({ 'background-color': 'transparent', 'background-image': 'none' });
            
            var videoSource = wp.customize('wporlogin_video_source') ? wp.customize('wporlogin_video_source').get() : 'external';
            var $videoContainer = $('#wporlogin-video-wrapper');

            // === CASO A: VIDEO EXTERNO (YouTube/Vimeo) ===
            if ( videoSource === 'external' ) {

                var videoUrl = wp.customize('wporlogin_video_external_url') ? wp.customize('wporlogin_video_external_url').get() : '';

                // [LIMPIEZA DE ZOMBIES]
                // Si hay un video LOCAL (<video>), lo eliminamos forzosamente para permitir que entre el iframe
                if ( $videoContainer.length && $videoContainer.find('video').length > 0 ) {
                    $videoContainer.remove();
                    $videoContainer = $('#wporlogin-video-wrapper'); // Actualizar referencia (estará vacía)
                }

                // Si no hay URL, eliminamos contenedor y salimos
                if ( ! videoUrl ) {
                    if ( $videoContainer.length ) $videoContainer.remove();
                    return;
                }

                // Usamos el Engine Global
                if ( window.WPORLoginVideo && typeof window.WPORLoginVideo.parseExternalVideo === 'function' ) {
                    
                    // FORZAMOS LA CONFIGURACIÓN NUEVA
                    // Al establecer esto manualmente, y con el arreglo en el otro archivo,
                    // el Engine respetará 'type: external' y 'videoUrl'.
                    window.WPORLoginVideo.config = {
                        type: 'external',
                        videoUrl: videoUrl,
                        overlayColor: rgba
                    };
                    window.WPORLoginVideo.init();
                }

            } 
            // === CASO B: VIDEO LOCAL ===
            else if ( videoSource === 'local' ) {
                
                // [LIMPIEZA DE ZOMBIES EXTERNOS]
                // Si hay un iframe, lo borramos.
                if ( $videoContainer.length && $videoContainer.find('iframe').length > 0 ) {
                    $videoContainer.remove();
                }
                
                // Nota: Para video local, generalmente el 'transport' => 'refresh' de PHP se encarga de renderizar,
                // pero si quisiéramos live preview de cambios locales, deberíamos implementarlo aquí también.
                // Por ahora, solo actualizamos el overlay.
                var $overlay = $('#wporlogin-video-overlay');
                if ( $overlay.length ) {
                    $overlay.css('background-color', rgba);
                }
            }
        }
        
        // --- MODO STATIC ---
        else if ( mode === 'static' ) {
            $('#wporlogin-video-wrapper').remove(); // Limpieza video

            var galleryImg = wp.customize('wporlogin_bg_gallery') ? wp.customize('wporlogin_bg_gallery').get() : '';
            var customImg  = wp.customize('wporlogin_login_background_image') ? wp.customize('wporlogin_login_background_image').get() : '';
            var finalImg   = ( customImg && customImg !== '' ) ? customImg : galleryImg;
            
            var bgRepeat   = wp.customize('wporlogin_login_background_repeat') ? wp.customize('wporlogin_login_background_repeat').get() : 'no-repeat';
            var bgSize     = wp.customize('wporlogin_login_background_size') ? wp.customize('wporlogin_login_background_size').get() : 'cover';
            var bgPosition = wp.customize('wporlogin_login_background_position') ? wp.customize('wporlogin_login_background_position').get() : 'center center';

            var bgString = (finalImg) 
                ? 'linear-gradient(' + rgba + ', ' + rgba + '), url(' + finalImg + ')' 
                : 'linear-gradient(' + rgba + ', ' + rgba + ')';

            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString,
                'background-repeat': bgRepeat,
                'background-size': bgSize,
                'background-position': bgPosition,
                'background-attachment': 'fixed'
            });
        } 
        
        // --- MODO DISABLED ---
        else {
            $('#wporlogin-video-wrapper').remove(); 
            var bgString = (overlayOpacity > 0) ? 'linear-gradient(' + rgba + ', ' + rgba + ')' : 'none';
            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString
            });
        }
    }

    var settings = [
        'wporlogin_background_mode', 
        'wporlogin_login_background_color',
        'wporlogin_bg_gallery', 
        'wporlogin_login_background_image',
        'wporlogin_video_source',     
        'wporlogin_video_external_url',
        'wporlogin_bg_overlay_color', 
        'wporlogin_bg_overlay_opacity', 
        'wporlogin_login_background_repeat', 
        'wporlogin_login_background_size', 
        'wporlogin_login_background_position'
    ];

    $.each(settings, function(i, id) {
        wp.customize(id, function(val) { val.bind(refreshBackground); });
    });

    wp.customize( 'wporlogin_random_duration', function( value ) {
        value.bind( function( newval ) {
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateDuration === 'function' ) {
                window.WPORLoginSlideshow.updateDuration( newval );
            }
        } );
    } );

    wp.customize.bind( 'preview-ready', function() { refreshBackground(); } );

})(jQuery);*/
/**
 * WPORLogin - Customizer JS (Live Preview)
 * OPTIMIZED: Separación de lógica de renderizado pesado vs ligero
 */
(function($) {
    'use strict';

    // Helper: Convertir Hex a RGBA
    function hexToRgba(hex, opacity) {
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c = hex.substring(1).split('');
            if(c.length === 3){ c = [c[0], c[0], c[1], c[1], c[2], c[2]]; }
            c = '0x'+c.join('');
            return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+(opacity/100)+')';
        }
        return 'rgba(0,0,0,0)';
    }

    // =================================================================
    // 1. FUNCIÓN LIGERA (Solo para Color y Opacidad - Cero Lag)
    // =================================================================
    function updateOverlayOnly() {
        var overlayColor    = wp.customize('wporlogin_bg_overlay_color').get();
        var overlayOpacity  = wp.customize('wporlogin_bg_overlay_opacity').get();
        var rgba            = hexToRgba(overlayColor, overlayOpacity);
        var mode            = wp.customize('wporlogin_background_mode') ? wp.customize('wporlogin_background_mode').get() : 'static';

        // A. Si es Modo Video (Local o Externo)
        // Simplemente actualizamos el div del overlay. Es rapidísimo.
        if ( mode === 'video' || mode === 'random' ) {
            var $vidOverlay = $('#wporlogin-video-overlay');
            if ( $vidOverlay.length ) {
                $vidOverlay.css('background-color', rgba);
            }
            // Si es random, avisamos al slideshow también
            if ( mode === 'random' && window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateOverlay === 'function' ) {
                window.WPORLoginSlideshow.updateOverlay( rgba );
            }
        } 
        
        // B. Si es Modo Estático
        // Aquí sí necesitamos reconstruir el string del gradiente, pero sin tocar el DOM innecesariamente.
        else if ( mode === 'static' ) {
             // Recuperamos la imagen actual del CSS para no tener que consultarla de nuevo
             // O simplemente forzamos un refreshBackground si el usuario prefiere (pero esto es más rápido)
             refreshBackground(); 
        }

        // C. MODO DISABLED (Solo Color) - ¡AGREGADO!
        // Aquí el overlay actúa como un tinte sobre el color de fondo.
        // Lo actualizamos directamente sin borrar ni recalcular nada más.
        else {
            var bgString = (overlayOpacity > 0) ? 'linear-gradient(' + rgba + ', ' + rgba + ')' : 'none';
            $('body.login').css('background-image', bgString);
        }
    }

    // =================================================================
    // 2. FUNCIÓN PESADA (Cambios de estructura DOM)
    // =================================================================
    function refreshBackground() {
        var bgColor         = wp.customize('wporlogin_login_background_color').get();
        var overlayColor    = wp.customize('wporlogin_bg_overlay_color').get();
        var overlayOpacity  = wp.customize('wporlogin_bg_overlay_opacity').get();
        var rgba            = hexToRgba(overlayColor, overlayOpacity);

        var mode = wp.customize('wporlogin_background_mode') ? wp.customize('wporlogin_background_mode').get() : 'static';

        // 1. LIMPIEZA GENERAL
        if ( mode !== 'random' ) {
            $('#wporlogin-slideshow-wrapper').remove();
        }

        // 2. LÓGICA POR MODO

        // --- MODO RANDOM ---
        if ( mode === 'random' ) {
            $('#wporlogin-video-wrapper').remove(); 
            $('body.login').css({ 'background-color': 'transparent', 'background-image': 'none' });
            
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateOverlay === 'function' ) {
                window.WPORLoginSlideshow.updateOverlay( rgba );
            }
        } 

        // --- MODO VIDEO (HÍBRIDO) ---
        else if ( mode === 'video' ) {
            
            $('body.login').css({ 'background-color': '#000000', 'background-image': 'none' });
            
            var videoSource = wp.customize('wporlogin_video_source') ? wp.customize('wporlogin_video_source').get() : 'external';
            var $videoContainer = $('#wporlogin-video-wrapper');

            // === CASO A: VIDEO EXTERNO ===
            if ( videoSource === 'external' ) {
                var videoUrl = wp.customize('wporlogin_video_external_url') ? wp.customize('wporlogin_video_external_url').get() : '';

                if ( $videoContainer.length && $videoContainer.find('video').length > 0 ) {
                    $videoContainer.remove();
                    $videoContainer = $('#wporlogin-video-wrapper');
                }

                if ( ! videoUrl ) {
                    if ( $videoContainer.length ) $videoContainer.remove();
                    return;
                }

                if ( window.WPORLoginVideo && typeof window.WPORLoginVideo.parseExternalVideo === 'function' ) {
                    window.WPORLoginVideo.config = {
                        type: 'external',
                        videoUrl: videoUrl,
                        overlayColor: rgba
                    };
                    window.WPORLoginVideo.init();
                }
            } 
            // === CASO B: VIDEO LOCAL ===
            else if ( videoSource === 'local' ) {
                
                if ( $videoContainer.length && $videoContainer.find('iframe').length > 0 ) {
                    $videoContainer.remove();
                }
                
                // Solo actualizamos overlay aquí, el PHP refresca el video si cambia el ID
                var $overlay = $('#wporlogin-video-overlay');
                if ( $overlay.length ) {
                    $overlay.css('background-color', rgba);
                }
            }
        }
        
        // --- MODO STATIC ---
        else if ( mode === 'static' ) {
            $('#wporlogin-video-wrapper').remove();

            var galleryImg = wp.customize('wporlogin_bg_gallery') ? wp.customize('wporlogin_bg_gallery').get() : '';
            var customImg  = wp.customize('wporlogin_login_background_image') ? wp.customize('wporlogin_login_background_image').get() : '';
            var finalImg   = ( customImg && customImg !== '' ) ? customImg : galleryImg;
            
            var bgRepeat   = wp.customize('wporlogin_login_background_repeat') ? wp.customize('wporlogin_login_background_repeat').get() : 'no-repeat';
            var bgSize     = wp.customize('wporlogin_login_background_size') ? wp.customize('wporlogin_login_background_size').get() : 'cover';
            var bgPosition = wp.customize('wporlogin_login_background_position') ? wp.customize('wporlogin_login_background_position').get() : 'center center';

            var bgString = (finalImg) 
                ? 'linear-gradient(' + rgba + ', ' + rgba + '), url(' + finalImg + ')' 
                : 'linear-gradient(' + rgba + ', ' + rgba + ')';

            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString,
                'background-repeat': bgRepeat,
                'background-size': bgSize,
                'background-position': bgPosition,
                'background-attachment': 'fixed'
            });
        } 
        
        // --- MODO DISABLED ---
        else if ( mode === 'disabled' ) {
            $('#wporlogin-video-wrapper').remove(); 
            var bgString = (overlayOpacity > 0) ? 'linear-gradient(' + rgba + ', ' + rgba + ')' : 'none';
            $('body.login').css({
                'background-color': bgColor,
                'background-image': bgString
            });
        }
    }

    // =================================================================
    // 3. BINDINGS INTELIGENTES
    // =================================================================
    
    // Grupo A: Configuraciones "Pesadas" -> Usan refreshBackground
    var heavySettings = [
        'wporlogin_background_mode', 
        'wporlogin_login_background_color',
        'wporlogin_bg_gallery', 
        'wporlogin_login_background_image',
        'wporlogin_video_source',     
        'wporlogin_video_external_url',
        'wporlogin_login_background_repeat', 
        'wporlogin_login_background_size', 
        'wporlogin_login_background_position'
    ];

    $.each(heavySettings, function(i, id) {
        wp.customize(id, function(val) { val.bind(refreshBackground); });
    });

    // Grupo B: Configuraciones "Ligeras" -> Usan updateOverlayOnly (¡Aquí está la magia!)
    // Al mover el slider, solo ejecutamos el código mínimo necesario.
    var lightSettings = [
        'wporlogin_bg_overlay_color', 
        'wporlogin_bg_overlay_opacity'
    ];

    $.each(lightSettings, function(i, id) {
        wp.customize(id, function(val) { val.bind(updateOverlayOnly); });
    });


    // Otros bindings
    wp.customize( 'wporlogin_random_duration', function( value ) {
        value.bind( function( newval ) {
            if ( window.WPORLoginSlideshow && typeof window.WPORLoginSlideshow.updateDuration === 'function' ) {
                window.WPORLoginSlideshow.updateDuration( newval );
            }
        } );
    } );

    wp.customize.bind( 'preview-ready', function() { refreshBackground(); } );

})(jQuery);