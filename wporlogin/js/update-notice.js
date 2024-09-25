jQuery(document).ready( function(){

    jQuery(document).on('click', '.delete_notice_wporlogin .notice-dismiss', function(){

        jQuery.ajax({
            type: 'post',
            url: notice_params_wporlogin.url,
            data:'action=' + notice_params_wporlogin.action + "&nonce=" + notice_params_wporlogin.nonce,

            error: function(response){
                console.log(response);
            }, success: function(response){
                console.log(response);
            }
        })

    });

} );