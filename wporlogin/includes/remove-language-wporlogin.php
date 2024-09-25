<?php
if(!defined('ABSPATH'))exit;

//PARA ELIMINAR MENÚ LANGUAGE
function remove_language_wporlogin_(){

    if( get_option("remove_language_wporlogin") == 1 ){

        add_filter( 'login_display_language_dropdown', '__return_false' );

    }

}
add_action('login_init','remove_language_wporlogin_');


//CUANDO MENÚ LANGUAGE "NO" ELIMINADO
function not_remove_language_wporlogin_style_login(){

    ?><style>
    :root{
        --wporlogin-login-justify-content: flex-start;
    }                    
    </style><?php 

}

function not_remove_language_wporlogin_style_register(){

    ?><style>
    :root{
        --wporlogin-register-justify-content: flex-start;
    }                    
    </style><?php 

}

function not_remove_language_wporlogin_style_lostpassword(){

    ?><style>
    :root{
        --wporlogin-lostpassword-justify-content: flex-start;
    }                    
    </style><?php 

}

//CUANDO MENÚ LANGUAGE ELIMINADO
function yes_remove_language_wporlogin_style_login(){

    ?><style>
    :root{
        --wporlogin-login-justify-content: center;
        --wporlogin-login-standard-min-height: 100vh;
    }                    
    </style><?php 

}

function yes_remove_language_wporlogin_style_register(){

    ?><style>
    :root{
        --wporlogin-register-justify-content: center;
        --wporlogin-register-standard-min-height: 100vh;
    }                    
    </style><?php 

}

function yes_remove_language_wporlogin_style_lostpassword(){

    ?><style>
    :root{
        --wporlogin-lostpassword-justify-content: center;
        --wporlogin-lostpassword-standard-min-height: 100vh;
    }                    
    </style><?php 

}


//YES reCAPTCHA V2 ACTIVO EN "LOGIN"
function yes_recaptcha_active_login_and_register_wporlogin_style(){

    if(get_option( 'recaptcha_v2_wporlogin' ) == 1){             
        if( get_option('recaptcha_v2_site_key_wporlogin' == 1) && get_option('recaptcha_v2_secret_key_wporlogin' == 1) ){

            yes_remove_language_wporlogin_style_login();
            yes_remove_language_wporlogin_style_register();

            yes_remove_language_wporlogin_style_login_min_height();
            yes_remove_language_wporlogin_style_register_min_height();

        } else {

            if(get_option('activa_acceso_recaptcha_v2_wporlogin') == 1){

                not_remove_language_wporlogin_style_login();

                not_remove_language_wporlogin_style_login_min_height();

            } else {
                yes_remove_language_wporlogin_style_login();

                yes_remove_language_wporlogin_style_login_min_height();

            }
            
            if(get_option('activa_registro_recaptcha_v2_wporlogin') == 1 ){

                not_remove_language_wporlogin_style_register();

                yes_remove_language_wporlogin_style_register_min_height();

            } else {
                yes_remove_language_wporlogin_style_register();

                yes_remove_language_wporlogin_style_register_min_height();
            }
            
        }
    } else{
        yes_remove_language_wporlogin_style_login();
        yes_remove_language_wporlogin_style_register();

        yes_remove_language_wporlogin_style_login_min_height();
        yes_remove_language_wporlogin_style_register_min_height();
    }

}



//CUANDO MENÚ LANGUAGE "NO" ELIMINADO
// MIN-HEIGHT
function not_remove_language_wporlogin_style_login_min_height(){

    ?><style>
    :root{
        --wporlogin-login-standard-min-height: auto;
    }                    
    </style><?php 

}

function not_remove_language_wporlogin_style_register_min_height(){

    ?><style>
    :root{
        --wporlogin-register-standard-min-height: auto;
    }                    
    </style><?php 
}

function not_remove_language_wporlogin_style_lostpassword_min_height(){

    ?><style>
    :root{
        --wporlogin-lostpassword-standard-min-height: auto;
    }                    
    </style><?php 

}

//CUANDO MENÚ LANGUAGE ELIMINADO
function yes_remove_language_wporlogin_style_login_min_height(){

    ?><style>
    :root{
        --wporlogin-login-standard-min-height: 100vh;
    }                    
    </style><?php 

}

function yes_remove_language_wporlogin_style_register_min_height(){

    ?><style>
    :root{
        --wporlogin-register-standard-min-height: 100vh;
    }                    
    </style><?php 
}

function yes_remove_language_wporlogin_style_lostpassword_min_height(){

    ?><style>
    :root{
        --wporlogin-lostpassword-standard-min-height: 100vh;
    }                    
    </style><?php 

}