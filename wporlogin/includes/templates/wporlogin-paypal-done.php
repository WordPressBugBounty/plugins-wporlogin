<!-- Archivo: templates/wporlogin-paypal-done.php -->

<!--BEGIN BOTÓN DE DONACIÓN CON PAYPAL-->
<div style="margin-top: 40px; border-bottom: solid 1px #005d8c; border-top: 1px #005d8c solid;">
    <div style="width: 90%; margin-left: auto; margin-right: auto;">                        
        <table class="form-table">                
            <tbody>
                <tr>
                    <th scope="row"><label><?php _e('Donate now', 'wporlogin') ?></label></th>
                    <td>
                        <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTG69VCQ5TZZN&source=url" rel="noopener noreferrer nofollow">
                            <img src="<?php echo esc_url('https://www.paypalobjects.com/es_ES/i/btn/btn_donate_SM.gif'); ?>">
                        </a>
                        <p class="description" id="tagline-description">
                            <strong><?php _e('Important', 'wporlogin'); ?>: </strong><?php _e('If you appreciate my work, consider making a small donation to support it. Your help is greatly appreciated!', 'wporlogin'); ?>
                        </p>
                        <p class="description" id="tagline-description"><?php _e('To donate, simply click the <strong>Donate</strong> button. Thank you! 😊', 'wporlogin'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>                        
    </div>
</div><!--END BOTÓN DE DONACIÓN CON PAYPAL-->