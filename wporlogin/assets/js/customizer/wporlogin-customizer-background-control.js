/**
 * Script para WPORLogin Multi Image Control para panel Izquierdo
 */
jQuery(document).ready(function($) {
    
    // 1. Abrir Media Uploader
    $('body').on('click', '.wporlogin-add-images', function(e) {
        e.preventDefault();
        var button = $(this);
        var controlId = button.data('target');
        var container = $('#container-' + controlId);
        
        // Crear instancia
        var frame = wp.media({
            title: 'Select Images for Slideshow',
            button: { text: 'Use selected images' },
            library: { type: 'image' },
            multiple: true
        });

        // Al seleccionar
        frame.on('select', function() {
            var selection = frame.state().get('selection');
            var input = $('#' + controlId);
            var currentIds = input.val() ? input.val().split(',') : [];

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                var idStr = attachment.id.toString();
                
                // Evitar duplicados visuales
                if ($.inArray(idStr, currentIds) === -1) {
                    currentIds.push(idStr);
                    
                    var url = attachment.url;
                    if(attachment.sizes && attachment.sizes.thumbnail) {
                        url = attachment.sizes.thumbnail.url;
                    }

                    container.append(
                        '<div class="wporlogin-thumb" data-id="' + idStr + '">' +
                        '<img src="' + url + '">' +
                        '<span class="wporlogin-remove" title="Remove">✕</span></div>'
                    );
                }
            });

            // Actualizar input y notificar al Customizer
            input.val(currentIds.join(',')).trigger('change');
        });

        frame.open();
    });

    // 2. Eliminar imagen
    $('body').on('click', '.wporlogin-remove', function() {
        var item = $(this).parent();
        var idToRemove = item.data('id').toString();
        var container = item.parent();
        // El ID del contenedor es "container-CONTROL_ID"
        var inputId = container.attr('id').replace('container-', '');
        var input = $('#' + inputId);

        var currentIds = input.val().split(',');
        // Filtrar el ID eliminado
        var newIds = currentIds.filter(function(id) {
            return id !== idToRemove && id !== "";
        });

        item.remove();
        input.val(newIds.join(',')).trigger('change');
    });

    // 3. Hacer sortable (Opcional - requiere jQuery UI Sortable si está cargado en Customizer)
    /*if($.fn.sortable) {
        $('.wporlogin-multi-images').sortable({
            stop: function() {
                var newIds = [];
                $(this).find('.wporlogin-thumb').each(function() {
                    newIds.push($(this).data('id'));
                });
                var inputId = $(this).attr('id').replace('container-', '');
                $('#' + inputId).val(newIds.join(',')).trigger('change');
            }
        });
    }*/
});