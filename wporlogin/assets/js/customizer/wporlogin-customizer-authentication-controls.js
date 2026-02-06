    // Variables globales dentro del scope para manejar la proporción
    let currentAspectRatio = null; 
    let isProgrammaticUpdate = false; // Bandera para evitar bucles infinitos

    // --- FUNCIÓN HELPER: CALCULAR RATIO ---
    // Extraemos esto para poder usarlo tanto al inicio como al cambiar imagen
    function calculateRatioFromUrl(url) {
        if (!url) {
            currentAspectRatio = null;
            return;
        }
        const img = new Image();
        img.onload = function() {
            let realW = this.width;
            let realH = this.height;
            if (realH > 0) {
                currentAspectRatio = realW / realH;
            }
        };
        img.src = url;
    }

    // 1. INICIALIZACIÓN (¡Esto faltaba!)
    // Al cargar el script, miramos si ya hay una imagen guardada
    wp.customize.bind('ready', function() {
        const currentImage = wp.customize('wporlogin_logo_image').get();
        if (currentImage) {
            calculateRatioFromUrl(currentImage);
        }
    });

    wp.customize('wporlogin_logo_image', function(value) {
        value.bind(function(newVal) {
            if (!newVal) {

                // Reset si no hay imagen
                currentAspectRatio = null;

                // Si borran el logo, reset a defaults
                updateSetting('wporlogin_logo_width', 84);
                updateSetting('wporlogin_logo_height', 84);
                updateSetting('wporlogin_logo_position', 'center top');
                updateSetting('wporlogin_logo_background_size', 'contain');
            } else {
                // Si suben logo, calculamos dimensiones inteligentes (Igual que en PHP Helper)
                const img = new Image();
                img.onload = function() {
                    let realW = this.width;
                    let realH = this.height;

                    
                    // Guardamos la proporción (Ancho / Alto)
                    currentAspectRatio = realW / realH;

                    let finalW, finalH;

                    // Lógica de sugerencia inicial (Tu lógica inteligente)
                    if (realW > realH) {
                        finalW = 250; 
                        finalH = Math.round(finalW / currentAspectRatio);
                    } else if (realH > realW) {
                        finalH = 90;
                        finalW = Math.round(finalH * currentAspectRatio);
                    } else {
                        finalW = 84;
                        finalH = 84;
                    }            

                    // Actualizamos sin disparar los cálculos recursivos
                    isProgrammaticUpdate = true;

                    // Actualizamos los campos (inputs) automáticamente
                    // El usuario verá estos números y podrá cambiarlos si quiere
                    updateSetting('wporlogin_logo_width', finalW);
                    updateSetting('wporlogin_logo_height', finalH);

                    isProgrammaticUpdate = false;
                };
                img.src = newVal;
            }
        });
    });

    // 2. ESCUCHAR CAMBIOS EN EL ANCHO (Calcula el Alto)
    wp.customize('wporlogin_logo_width', function(value) {
        value.bind(function(newWidth) {
            // Solo calculamos si hay una imagen, hay proporción y NO es una actualización automática
            if (currentAspectRatio && !isProgrammaticUpdate && newWidth > 0) {
                
                // Bloqueamos para que el cambio de altura no dispare de nuevo este evento
                isProgrammaticUpdate = true;
                
                // Fórmula: Alto = Ancho / Ratio
                let newHeight = Math.round(newWidth / currentAspectRatio);
                
                updateSetting('wporlogin_logo_height', newHeight);
                
                // Desbloqueamos
                isProgrammaticUpdate = false;
            }
        });
    });

    // 3. ESCUCHAR CAMBIOS EN EL ALTO (Calcula el Ancho)
    wp.customize('wporlogin_logo_height', function(value) {
        value.bind(function(newHeight) {
            if (currentAspectRatio && !isProgrammaticUpdate && newHeight > 0) {
                
                isProgrammaticUpdate = true;
                
                // Fórmula: Ancho = Alto * Ratio
                let newWidth = Math.round(newHeight * currentAspectRatio);
                
                updateSetting('wporlogin_logo_width', newWidth);
                
                isProgrammaticUpdate = false;
            }
        });
    });

    // Helper para actualizar valor y visual
    function updateSetting(id, value) {
        wp.customize(id, function(setting) {
            setting.set(value);
        });
        jQuery('input[data-customize-setting-link="' + id + '"]').val(value).trigger('change');
    }
/*wp.customize('wporlogin_logo_image', function(value) {
    value.bind(function(newVal) {
        if (!newVal) {
            wp.customize('wporlogin_logo_width', function(setting) {
                setting.set(84);
                jQuery('input[data-customize-setting-link="wporlogin_logo_width"]').val(84).trigger('change');
            });
            wp.customize('wporlogin_logo_height', function(setting) {
                setting.set(84);
                jQuery('input[data-customize-setting-link="wporlogin_logo_height"]').val(84).trigger('change');
            });
            wp.customize('wporlogin_logo_position', function(setting) {
                setting.set('center top');
                jQuery('select[data-customize-setting-link="wporlogin_logo_position"]').val('center top').trigger('change');
            });
            wp.customize('wporlogin_logo_background_size', function(setting) {
                setting.set('contain');
                jQuery('select[data-customize-setting-link="wporlogin_logo_background_size"]').val('contain').trigger('change');
            });
        } else {
            // Si se agrega un logo → leer ancho y alto reales
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;
                
                // Si el ancho supera 200px, lo limitamos y ajustamos el alto proporcional
                if (width > 200) {
                    const ratio = height / width;
                    width = 200;
                    height = Math.round(width * ratio);
                }

                wp.customize('wporlogin_logo_width', function(setting) {
                    setting.set(width);
                    jQuery('input[data-customize-setting-link="wporlogin_logo_width"]').val(width).trigger('change');
                });
                wp.customize('wporlogin_logo_height', function(setting) {
                    setting.set(height);
                    jQuery('input[data-customize-setting-link="wporlogin_logo_height"]').val(height).trigger('change');
                });
            };
            img.src = newVal;
        }
    });
});*/
