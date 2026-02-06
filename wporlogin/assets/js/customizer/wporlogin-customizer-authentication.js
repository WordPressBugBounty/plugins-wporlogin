/**
 * WPORLogin - Personalizador de la Sección de Autenticación (Vista previa en vivo)
 *
 * @package WPORLogin
 */
(function($) {
    'use strict';

    const loginBox = $('body.login #login');
    const loginActions = $('body.login-action-login, body.login-action-register, body.login-action-lostpassword, body.login-action-confirm_admin_email');
    const loginLogo = $('body.login div#login h1');
    const loginLogoLink = $('body.login h1 a');    

    /**
     * Aplica centrado del contenido.
     *
     * @param {string} value - Valor: 'center' para centrar, otro valor para estilo normal.
     */
    function applyCenterContent(value) {
        
        if (value === 'center') {
            loginActions.css({
                'display': 'flex',
                'flex-direction': 'column',
                'align-items': 'center'
            });
            
            $('body.login-action-confirm_admin_email #login').css('margin-top', 'auto');
            
        } else if (value === 'default') {
            loginActions.css({
                'display': 'block',
                'flex-direction': 'initial',
                'align-items': 'initial'
            });
            
            $('body.login-action-confirm_admin_email #login').css('margin-top', '-2vh');
        }
    }

    /**
     * Convierte color a formato rgba según opacidad.
     *
     * @param {string} color - Color en formato rgb o hex.
     * @param {string} opacity - Valor de opacidad (0 a 1).
     * @returns {string} - Color en formato rgba.
     */
    function convertToRgba(color, opacity) {
        if (color.startsWith('rgb')) {
            const rgb = color.match(/\d+/g);
            return `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${opacity})`;
        }

        const hex = color.replace('#', '');
        const bigint = parseInt(hex, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;

        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    /**
     * Aplica el color de fondo.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyBackgroundColor(color) {
        // Recuperamos la opacidad actual desde la configuración de WP
        var currentOpacity = wp.customize('wporlogin_auth_opacity') ? wp.customize('wporlogin_auth_opacity').get() : '1';
        
        // Aplicamos el color convertido a RGBA con la opacidad correcta
        loginBox.css('background-color', convertToRgba(color, currentOpacity));
    }

    /**
     * Aplica opacidad al fondo.
     *
     * @param {string} opacity - Opacidad de 0 a 1.
     */
    function applyBackgroundOpacity(opacity) {
        // 1. Intentamos obtener el valor RAW (Hexadecimal) desde el Customizer
        // Usamos 'wporlogin_auth_bg_color' que es el ID de tu configuración de color
        var bgColor = wp.customize('wporlogin_auth_bg_color') ? wp.customize('wporlogin_auth_bg_color').get() : '';

        // 2. Fallback: Si no hay color configurado, usamos blanco por defecto
        if (!bgColor) {
            bgColor = '#ffffff';
        }

        // 3. Aplicamos la conversión usando el color puro + la nueva opacidad
        loginBox.css('background-color', convertToRgba(bgColor, opacity));
    }

    /**
     * Aplica radio de borde.
     *
     * @param {string} radius - Valor de border-radius.
     */
    function applyBorderRadius(radius) {
        loginBox.css('border-radius', radius);
    }

    /**
     * Aplica padding.
     *
     * @param {string} position - top, right, bottom, left.
     * @param {string} value - Valor de padding.
     */
    function applyPadding(position, value) {
        loginBox.css(`padding-${position}`, value);
    }

    /**
     * Aplica ancho de borde.
     *
     * @param {string} width - Ancho en píxeles.
     */
    function applyBorderWidth(width) {
        loginBox.css('border-width', `${width}px`);
    }

    /**
     * Aplica estilo de borde.
     *
     * @param {string} style - Estilo de borde (solid, dashed, etc).
     */
    function applyBorderStyle(style) {
        loginBox.css('border-style', style);
    }

    /**
     * Aplica color de borde.
     *
     * @param {string} color - Color hexadecimal.
     */
    function applyBorderColor(color) {
        loginBox.css('border-color', color);
    }    
    
    /**
     * Aplica el logo de login dinámicamente.
     *
     * @param {string} imageUrl - URL de la imagen personalizada o vacío para restaurar el logo estándar de WordPress.
     */
    function applyLoginLogo(imageUrl) {
        if (imageUrl) {

            // Logo personalizado
            loginLogoLink.css({
                'background-image': `url(${imageUrl})`,
                'background-size': 'contain',
                'background-repeat': 'no-repeat',
                'background-position': 'center center',
                'display': 'block',
                'width': '',
                'height': ''
            });
        } else {

            // --- CASO B: NO HAY IMAGEN (Se eliminó) -> RESTAURAR WORDPRESS ---

            // 1. Determinar la URL del logo de WordPress
            // Intentamos obtenerla de la variable PHP (si la pasaste), si no, usamos la ruta por defecto.
            let wpLogoUrl = '/wp-admin/images/wordpress-logo.svg';
            
            if (typeof wporloginParams !== 'undefined' && wporloginParams.adminUrl) {
                wpLogoUrl = wporloginParams.adminUrl + 'images/wordpress-logo.svg';
            }

            // 2. Aplicar los estilos originales de WordPress
            loginLogoLink.css({
                'background-image': `url(${wpLogoUrl})`, // <--- CORREGIDO: Usamos la URL de WP, no imageUrl (que está vacía)
                'background-size': '84px',
                'background-position': 'center top',
                'background-repeat': 'no-repeat',
                'display': 'block',
                'width': '84px',
                'height': '84px',
                'text-indent': '-9999px',
                'margin': '0 auto 25px auto', // Margen estándar de WP
                'border-radius': '0'          // Reseteamos bordes por si acaso
            });          
        }        
    }

    /**
    * Aplica el ancho del logotipo.
    *
    * @param {string} width - Ancho en píxeles.
    */
   function applyLogoWidth(width) {
       loginLogoLink.css('width', width ? width + 'px' : '');
   }

   /**
    * Aplica la altura del logotipo.
    *
    * @param {string} height - Altura en píxeles.
    */
   function applyLogoHeight(height) {
       loginLogoLink.css('height', height ? height + 'px' : '');
   }

   /**
    * Aplica la posición de fondo del logotipo.
    *
    * @param {string} position - background-position CSS.
    */
   function applyLogoPosition(position) {
       loginLogoLink.css('background-position', position);
   }

   /**
    * Aplica el tamaño de fondo del logotipo.
    *
    * @param {string} size - background-size CSS.
    */
   function applyLogoBackgroundSize(size) {
       loginLogoLink.css('background-size', size);
   }

    /**
     * Aplica la visibilidad del logotipo.
     *
     * @param {string} display - 'block' o 'none'.
     */
    function applyLogoVisibility(display) {
        loginLogo.css('display', display);
    }

    // === Escuchar cambios en el Personalizador === //
    
    wp.customize('wporlogin_auth_center_content', function(value) {
        value.bind(function(newVal) {
            applyCenterContent(newVal);
        });
        applyCenterContent(value.get()); // <--- PERSISTENCIA
    });

    wp.customize('wporlogin_auth_bg_color', function(value) {
        value.bind(function(newVal) {
            applyBackgroundColor(newVal);
        });
        applyBackgroundColor(value.get());
    });

    wp.customize('wporlogin_auth_opacity', function(value) {
        value.bind(function(newVal) {
            applyBackgroundOpacity(newVal);
        });
        applyBackgroundOpacity(value.get());
    });

    wp.customize('wporlogin_auth_border_radius', function(value) {
        value.bind(function(newVal) {
            applyBorderRadius(newVal);
        });
        applyBorderRadius(value.get());
    });

    ['top', 'right', 'bottom', 'left'].forEach(function(pos) {
        wp.customize(`wporlogin_auth_padding_${pos}`, function(value) {
            value.bind(function(newVal) {
                applyPadding(pos, newVal);
            });
            applyPadding(pos, value.get());
        });
    });

    wp.customize('wporlogin_auth_border_width', function(value) {
        value.bind(function(newVal) {
            applyBorderWidth(newVal);
        });
        applyBorderWidth(value.get());
    });

    wp.customize('wporlogin_auth_border_style', function(value) {
        value.bind(function(newVal) {
            applyBorderStyle(newVal);
        });
        applyBorderStyle(value.get());
    });

    wp.customize('wporlogin_auth_border_color', function(value) {
        value.bind(function(newVal) {
            applyBorderColor(newVal);
        });
        applyBorderColor(value.get());
    });
    
    // Detectar cambios del logo en tiempo real
    wp.customize('wporlogin_logo_image', function(value) {
        value.bind(function(newVal) {
            applyLoginLogo(newVal);
        });
        applyLoginLogo(value.get());
    });
    
    wp.customize('wporlogin_logo_width', function(value) {
        value.bind(function(newVal) {
            applyLogoWidth(newVal);
        });
        applyLogoWidth(value.get());
    });

    wp.customize('wporlogin_logo_height', function(value) {
        value.bind(function(newVal) {
            applyLogoHeight(newVal);
        });
        applyLogoHeight(value.get());
    });

    wp.customize('wporlogin_logo_position', function(value) {
        value.bind(function(newVal) {
            applyLogoPosition(newVal);
        });
        applyLogoPosition(value.get());
    });

    wp.customize('wporlogin_logo_background_size', function(value) {
        value.bind(function(newVal) {
            applyLogoBackgroundSize(newVal);
        });
        applyLogoBackgroundSize(value.get());
    });


    wp.customize('wporlogin_logo_visible', function(value) {
        value.bind(function(newVal) {
            applyLogoVisibility(newVal);
        });
        applyLogoVisibility(value.get());
    });

})(jQuery);