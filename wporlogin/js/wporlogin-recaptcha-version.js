document.addEventListener('DOMContentLoaded', function() {
    const recaptchaVersionSelect = document.getElementById('recaptcha_version_wporlogin');
    const v2Fields = document.querySelectorAll('.wporlogin-recaptcha-v2-fields');
    const v3Fields = document.querySelectorAll('.wporlogin-recaptcha-v3-fields');

    function toggleRecaptchaFields() {
        const selectedVersion = recaptchaVersionSelect.value;

        // Mostrar/ocultar campos de v2 y v3 según la selección
        if (selectedVersion === 'v2') {
            v2Fields.forEach(field => field.style.display = '');
            v3Fields.forEach(field => field.style.display = 'none');
        } else if (selectedVersion === 'v3') {
            v2Fields.forEach(field => field.style.display = 'none');
            v3Fields.forEach(field => field.style.display = '');
        } else {
            v2Fields.forEach(field => field.style.display = 'none');
            v3Fields.forEach(field => field.style.display = 'none');
        }
    }

    // Ejecutar la función al cargar la página
    toggleRecaptchaFields();

    // Escuchar cambios en el selector de la versión
    recaptchaVersionSelect.addEventListener('change', toggleRecaptchaFields);
});
