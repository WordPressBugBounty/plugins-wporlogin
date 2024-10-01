<!-- Archivo: templates/wporlogin-paypal-done.php -->

<!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL-->
<div style="margin-top: 40px; border-bottom: solid 1px #005d8c; border-top: 1px #005d8c solid;">
    <div style="width: 90%; margin-left: auto; margin-right: auto;">                        
        <table class="form-table">                
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Donar ahora', 'wporlogin') ?></label></th>
                    <td>
                        <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTG69VCQ5TZZN&source=url" rel="noopener noreferrer nofollow">
                            <img src="<?php echo esc_url('https://www.paypalobjects.com/es_ES/i/btn/btn_donate_SM.gif'); ?>">
                        </a>
                        <p class="description" id="tagline-description">
                            <strong><?php _e('Importante', 'wporlogin'); ?>: </strong><?php _e('Si valoras mi trabajo, considera hacer una pequeña donación para mostrar tu aprecio. ¡Gracias!', 'wporlogin'); ?>
                        </p>
                        <p class="description" id="tagline-description"><?php _e('Para donar ahora, haz clic en el botón de Donar', 'wporlogin'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>                        
    </div>
</div><!--END BOTÓN DE DONACIÓN CON PAYPAL-->

