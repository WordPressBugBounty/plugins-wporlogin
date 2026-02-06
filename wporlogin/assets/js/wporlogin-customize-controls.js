/**
 * WPORLogin - Control de Navegación y Seguridad (FUSIÓN)
 * Maneja el cambio de plantillas y protege contra pérdida de cambios.
 *//*
(function(api) {
    'use strict';

    // =======================================================
    // 1. EL PORTERO: INTERCEPTOR DE CLICS DE SEGURIDAD
    // (Se ejecuta ANTES que la navegación para proteger tus cambios)
    // =======================================================
    api.bind('ready', function() {
        
        // IDs de las secciones y paneles que vamos a proteger
        const protectedAreas = [
            'wporlogin_sectionRegister', 
            'wporlogin_sectionRecovery', 
            'wporlogin_sectionEmailConfirm',
            'wporlogin_panel_login' // También el panel principal
        ];

        protectedAreas.forEach(function(id) {
            
            // Determinamos si es Sección o Panel para buscar el ID correcto en el DOM
            // WordPress usa 'accordion-section-{id}' o 'accordion-panel-{id}'
            let elementId = 'accordion-section-' + id;
            if (id.indexOf('panel') !== -1) {
                elementId = 'accordion-panel-' + id;
            }

            const container = document.getElementById(elementId);

            if (container) {
                
                // A) Proteger la ENTRADA (Clic en el título para abrir)
                const title = container.querySelector('.accordion-section-title, .accordion-panel-title');
                
                if (title) {
                    // useCapture = true (Fase de captura para ganar prioridad)
                    title.addEventListener('click', function(e) {
                        
                        // Si hay cambios sin guardar (Dirty state)
                        if ( ! api.state('saved').get() ) {
                            
                            var msg = "⚠️ ¡TIENES CAMBIOS SIN GUARDAR!\n\nSi cambias de vista, se recargará la página y perderás tus ajustes.\n\n[Cancelar] = Quedarse y guardar.\n[Aceptar] = Descartar y continuar.";
                            
                            if ( ! confirm(msg) ) {
                                // DETENEMOS TODO. La sección no se abrirá. La URL no cambiará.
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                            }
                        }
                    }, true); 
                }

                // B) Proteger la SALIDA (Clic en botón "Atrás")
                const backBtn = container.querySelector('.customize-section-back, .customize-panel-back');
                
                if (backBtn) {
                    backBtn.addEventListener('click', function(e) {
                         if ( ! api.state('saved').get() ) {
                            if ( ! confirm("⚠️ ¡TIENES CAMBIOS SIN GUARDAR!\n\n¿Deseas guardar antes de volver?") ) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                            }
                        }
                    }, true);
                }
            }
        });
    });


    // =======================================================
    // 2. LA NAVEGACIÓN (TU LÓGICA ORIGINAL)
    // Si el "Portero" de arriba deja pasar el clic, esto se ejecuta.
    // =======================================================

    // Función para cambiar la vista previa según el panel expandido
    function updatePreviewOnPanelExpand(panelId) {
        api.panel(panelId, function(panel) {
            panel.expanded.bind(function(isExpanded) {
                if (isExpanded) {
                    // Entrando al Panel Principal -> Cargar Login
                    let previewUrl = wporloginControlsSettings.loginPageUrl + "?template=login";                    
                    api.previewer.previewUrl.set(previewUrl);
                    
                } else {
                    // Saliendo del Panel -> Volver al Home
                    api.previewer.previewUrl.set(api.settings.url.home);
                }
            });
        });
    }

    // Función para cambiar la vista previa según sección expandida
    function updatePreviewOnSectionExpand(sectionId, templateType) {
        api.section(sectionId, function(section) {
            section.expanded.bind(function(isExpanded) {
                if (isExpanded) {
                    // Entrando a Sección -> Cargar Plantilla (Register/Recovery/etc)
                    let previewUrl = wporloginControlsSettings.loginPageUrl + "?template=" + templateType;                    
                    api.previewer.previewUrl.set(previewUrl);

                } else {
                    // Saliendo de Sección -> Volver a Login
                    api.previewer.previewUrl.set(wporloginControlsSettings.loginPageUrl + "?template=login");
                }
            });
        });
    }

    // --- Inicializar Navegación ---
    
    // Panel Principal
    updatePreviewOnPanelExpand("wporlogin_panel_login");

    // Secciones Específicas
    updatePreviewOnSectionExpand("wporlogin_sectionRegister", "register");
    updatePreviewOnSectionExpand("wporlogin_sectionRecovery", "recovery");
    updatePreviewOnSectionExpand("wporlogin_sectionEmailConfirm", "emailconfirm");

})(wp.customize);*/

(function(api) {
    // Función para cambiar la vista previa según el panel expandido
    function updatePreviewOnPanelExpand(panelId) {
        api.panel(panelId, function(panel) {
            panel.expanded.bind(function(isExpanded) {
                if (isExpanded) {

                    // Cambia la URL de la vista previa agregando el parámetro 'template'
                    let previewUrl = wporloginControlsSettings.loginPageUrl + "?template=login";                    
                    api.previewer.previewUrl.set(previewUrl);
                    
                } else {
                    // Si se colapsa el panel, volver a la página principal
                    api.previewer.previewUrl.set(api.settings.url.home);
                }
            });
        });
    }

    // Función para cambiar la vista previa según sección expandido
    function updatePreviewOnSectionExpand(sectionId, templateType) {
        api.section(sectionId, function(section) {
            section.expanded.bind(function(isExpanded) {
                if (isExpanded) {

                    // Cambia la URL de la vista previa agregando el parámetro 'template'
                    let previewUrl = wporloginControlsSettings.loginPageUrl + "?template=" + templateType;                    
                    api.previewer.previewUrl.set(previewUrl);

                } else {
                    // Si se colapsa el panel, volver a la página principal
                    api.previewer.previewUrl.set(wporloginControlsSettings.loginPageUrl + "?template=login");
                }
            });
        });
    }

    // Asociar el panel principal con la plantilla de login
    updatePreviewOnPanelExpand("wporlogin_panel_login");

    // Asociar las secciones de registro y recuperación con sus plantillas
    updatePreviewOnSectionExpand("wporlogin_sectionRegister", "register");
    updatePreviewOnSectionExpand("wporlogin_sectionRecovery", "recovery");
    updatePreviewOnSectionExpand("wporlogin_sectionEmailConfirm", "emailconfirm");

})(wp.customize);