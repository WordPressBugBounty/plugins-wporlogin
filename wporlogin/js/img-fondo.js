jQuery(document).ready(function($){
        const { __, _x, _n, sprintf } = wp.i18n;
	var mediaUploaderLogo;
	var mediaUploader;
    var logoWidth;

        /*  
         *  ///////////////////////////////////////////////////////////////////////
         *  Script de JavaScript para seleccionar Logotipo, de la biblioteca
         *  de WordPress. 
         *  ///////////////////////////////////////////////////////////////////////
         *  https://codex.wordpress.org/Javascript_Reference/wp.media
         *  https://decodecms.com/usar-el-media-uploader-de-wordpress-en-tus-plugins-y-temas/
         *  https://webkul.com/blog/how-to-use-wordpress-media-upload-in-plugin-theme/
         */
	$('#wporlogin_url_logotipo_button').click(function(e) {
            e.preventDefault();

            // Si el objeto del cargador ya se ha creado, vuelva a abrir el cuadro de diálogo
            if (mediaUploaderLogo) {
                mediaUploaderLogo.open();
                return;
            }

            // Extiende el objeto wp.media
            mediaUploaderLogo = wp.media.frames.file_frame = wp.media({
                title: _x('Logo', 'wporlogin'),
                button: {
                text: _x('Select logo', 'wporlogin')
            }, multiple: false });

            // Cuando se selecciona un archivo, toma la URL y configúra como el valor del campo de texto
            mediaUploaderLogo.on('select', function() {
                attachment = mediaUploaderLogo.state().get('selection').first().toJSON();
                $('#wporlogin_url_logotipo_text').val(attachment.url);
                $('#wporlogin_width_logotipo_text').val(attachment.width);                
                $('#wporlogin_height_logotipo_text').val(attachment.height);
                //console.log( attachment );
                $('#wporlogin_url_logotipo_img').attr('src', attachment.url);
                jQuery('#wporlogin_url_logotipo_img').css("display", "block"); //Mostrar
            });

            // Abre el diálogo del cargador
            mediaUploaderLogo.open();        
	});
        
                
        /*  
        *  ///////////////////////////////////////////////////////////////////////
        *  Script de JavaScript para seleccionar imagen de fondo, de la biblioteca
        *  de WordPress. 
        *  ///////////////////////////////////////////////////////////////////////
        */
        $('#wporlogin_url_img_fondo_button').click(function(e) {
            e.preventDefault();

            // Si el objeto del cargador ya se ha creado, vuelva a abrir el cuadro de diálogo
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Extiende el objeto wp.media
            mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: _x('Background image', 'wporlogin'),
                    button: {
                    text: _x('Select background image', 'wporlogin')
                }, multiple: false });

            // Cuando se selecciona un archivo, toma la URL y configúra como el valor del campo de texto
            mediaUploader.on('select', function() {
                    attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#wporlogin_url_img_fondo_text').val(attachment.url);                    
                    $('#wporlogin_url_img_fondo_img').attr('src', attachment.url);
		});

            // Abre el diálogo del cargador
            mediaUploader.open();
	});
        
        
        
        
        /*
         * Script JQuery para selecionar opciones
         */
        jQuery('#wporlogin_design_basic').click(function(){
            
            jQuery('#wporlogin-container-basic').show();//Mostrar
            jQuery('#wporlogin-container-basic-triangulo').show();//Mostrar

            jQuery('#wporlogin-container-standard').hide();//Ocultar
            jQuery('#wporlogin-container-standard-triangulo').hide();//Ocultar
            jQuery('#wporlogin-container-standard-premium').hide();//Ocultar
            jQuery('#wporlogin-container-premium').hide();//Ocultar
            jQuery('#wporlogin-container-premium-triangulo').hide();//Ocultar
            
        });
        jQuery('#wporlogin_design_standard').click(function(){
            
            jQuery('#wporlogin-container-standard').show();//Mostrar
            jQuery('#wporlogin-container-standard-triangulo').show();//Mostrar
            jQuery('#wporlogin-container-standard-premium').show();//Mostrar

            jQuery('#wporlogin-container-premium').hide();//Ocultar
            jQuery('#wporlogin-container-premium-triangulo').hide();//Ocultar
            jQuery('#wporlogin-container-basic').hide();//Ocultar
            jQuery('#wporlogin-container-basic-triangulo').hide();//Ocultar
            
        });
        jQuery('#wporlogin_design_premium').click(function(){
            
            jQuery('#wporlogin-container-premium').show();//Mostrar
            jQuery('#wporlogin-container-premium-triangulo').show();//Mostrar
            jQuery('#wporlogin-container-standard-premium').show();//Mostrar

            jQuery('#wporlogin-container-standard').hide();//Ocultar
            jQuery('#wporlogin-container-standard-triangulo').hide();//Ocultar
            jQuery('#wporlogin-container-basic').hide();//Ocultar
            jQuery('#wporlogin-container-basic-triangulo').hide();//Ocultar
            
        });
        
        
        
        /*
         * Script JQuery para selecionar opciones images
         */
        jQuery('#wporlogin_free_images').click(function(){
            
            jQuery('#wporlogin-container-background-free-image').show();//Mostrar
            jQuery('#wporlogin-container-background-my-image').hide();//Ocultar
            
        });
        jQuery('#wporlogin_my_images').click(function(){
            
            jQuery('#wporlogin-container-background-my-image').show();//Mostrar
            jQuery('#wporlogin-container-background-free-image').hide();//Ocultar
            
        });
        
        
        
});