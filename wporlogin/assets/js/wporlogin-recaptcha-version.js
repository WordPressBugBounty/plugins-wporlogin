document.addEventListener('DOMContentLoaded', function() {
    
    // Obtener los elementos del DOM
    const recaptchaVersionSelect = document.getElementById('recaptcha_version_wporlogin');
    const v2Fields = document.querySelectorAll('.wporlogin-recaptcha-v2-fields');
    const v3Fields = document.querySelectorAll('.wporlogin-recaptcha-v3-fields');

    // BLINDAJE DE SEGURIDAD:
    // Si el selector no existe en esta página, detenemos el script para evitar errores de consola.
    if (!recaptchaVersionSelect) {
        return;
    }

    /**
     * Alterna la visibilidad de los campos según la versión seleccionada.
     */
    function toggleRecaptchaFields() {
        const selectedVersion = recaptchaVersionSelect.value;

        if (selectedVersion === 'v2') {
            // Mostrar V2, Ocultar V3
            v2Fields.forEach(field => field.style.display = ''); // '' vuelve al valor por defecto (table-row)
            v3Fields.forEach(field => field.style.display = 'none');
        } else if (selectedVersion === 'v3') {
            // Ocultar V2, Mostrar V3
            v2Fields.forEach(field => field.style.display = 'none');
            v3Fields.forEach(field => field.style.display = '');
        } else {
            // Ocultar ambos (Desactivado)
            v2Fields.forEach(field => field.style.display = 'none');
            v3Fields.forEach(field => field.style.display = 'none');
        }
    }

    // Ejecutar al cargar la página para establecer el estado inicial
    toggleRecaptchaFields();

    // Escuchar cambios en el selector
    recaptchaVersionSelect.addEventListener('change', toggleRecaptchaFields);
});
