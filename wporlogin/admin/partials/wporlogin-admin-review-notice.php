<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plantilla para la notificación de solicitud de reseña.
 */
?>
<div class="notice notice-success is-dismissible jpum-notice delete_notice_wporlogin" style="background: #ffffff; border: 1px solid rgba(108, 26, 107, 0.15); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); padding: 26px 30px; display: flex; align-items: flex-start; border-radius: 14px; gap: 20px; margin-top: 24px; ">

    <img src="<?php echo esc_url( WPORLOGIN_URL . 'assets/img/logo-wporlogin.png' ); ?>" alt="WPOrLogin" style="width: 70px; height: auto; border-radius: 12px; flex-shrink: 0;">

    <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; color: #1d1d1f;">
        <p style="margin: 0 0 5px; font-size: 17px; font-weight: 600;">
            <?php echo esc_html__('Hello! You’ve been using ', 'wporlogin'); ?>
            <span style="font-weight: 600; color: #6c1a6b;">WPOrLogin</span>
            <?php echo esc_html__(' on your site for 2 weeks — we hope it’s been helpful.', 'wporlogin'); ?>
        </p>

        <p style="margin: 0 0 10px; color: #4d4d4d; font-size: 15.2px;">
            <?php echo esc_html__('If WPOrLogin has been useful, would you consider sharing an honest review? Your feedback helps us improve.', 'wporlogin'); ?>
        </p>

        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 14.5px; color: #4d4d4d; font-weight: bold;">
                <?php echo esc_html__('Share your experience', 'wporlogin'); ?>
            </span>

            <a href="https://wordpress.org/support/plugin/wporlogin/reviews/" 
               class="jpum-dismiss" target="_blank" data-reason="am_now" style="display: inline-block; background: #6c1a6b; color: #fff; font-size: 15px; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: background 0.2s ease;">
                <?php echo esc_html__('Write a review', 'wporlogin'); ?>
            </a>
        </div>
    </div>
</div>