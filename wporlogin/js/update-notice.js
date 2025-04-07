jQuery(document).ready(function($) {

    $(document).on('click', '.delete_notice_wporlogin .notice-dismiss', function(e) {
        e.preventDefault(); // prevenir comportamientos inesperados

        $.ajax({
            type: 'post',
            url: notice_params_wporlogin.url,
            data: {
                action: notice_params_wporlogin.action,
                nonce: notice_params_wporlogin.nonce
            },
            success: function(response) {
                console.log('Notificación descartada correctamente:', response);
            },
            error: function(response) {
                console.log('Error al descartar notificación:', response);
            }
        });

    });

});
