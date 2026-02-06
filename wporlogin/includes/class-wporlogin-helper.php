<?php

/**
 * Clase de ayuda con funciones estáticas reutilizables.
 *
 * @package    Wporlogin
 * @subpackage Wporlogin/includes
 */
class Wporlogin_Helper {

    /**
     * Calcula inteligentemente las dimensiones del logo.
     * * Lógica:
     * 1. Si hay valores manuales (Custom Design), los respeta.
     * 2. Si no hay valores (Predefined Design), calcula la proporción (Paisaje/Retrato).
     *
     * @param string $url           URL de la imagen.
     * @param string $manual_width  Ancho manual deseado (fallback).
     * @param string $manual_height Alto manual deseado (fallback).
     * @return array                Arreglo con 'width' y 'height' (ej. '250px').
     */
    public static function get_smart_logo_dimensions( $url, $manual_width = '', $manual_height = '' ) {
        
        // =========================================================
        // 1. PRIORIDAD: EL USUARIO MANDA (Para Diseño Custom)
        // =========================================================
        
        // Convertimos a entero para asegurar que sean números
        $w_int = intval( $manual_width );
        $h_int = intval( $manual_height );

        // Si el usuario guardó valores válidos (mayores a 0), los usamos y TERMINAMOS.
        if ( $w_int > 0 && $h_int > 0 ) {
             return array( 
                 'width'  => $w_int . 'px', 
                 'height' => $h_int . 'px' 
             );
        }

        // =========================================================
        // 2. FALLBACK: CÁLCULO AUTOMÁTICO (Para Diseños Predefinidos)
        // =========================================================
        // Aquí entra cuando $manual_width está vacío (lo cual es correcto en Predefinidos)

        // Valores por defecto (cuadrado pequeño)
        $width_css  = '84px'; 
        $height_css = '84px';

        if ( empty( $url ) ) {
            return array( 'width' => $width_css, 'height' => $height_css );
        }

        // --- RESTAURANDO LA LÓGICA QUE FALTABA ---
        
        $attachment_id = attachment_url_to_postid( $url );
        $meta_data     = $attachment_id ? wp_get_attachment_metadata( $attachment_id ) : false;

        if ( $meta_data && isset( $meta_data['width'], $meta_data['height'] ) ) {
            $real_w = $meta_data['width'];
            $real_h = $meta_data['height'];

            if ( $real_w > $real_h ) {
                // CASO A: Paisaje (Horizontal) -> Forzamos ancho 250px
                $width_css  = '250px';
                $ratio      = $real_h / $real_w;
                $height_css = round( $ratio * 250 ) . 'px';

            } elseif ( $real_h > $real_w ) {
                // CASO B: Retrato (Vertical) -> Forzamos alto 90px
                $height_css = '90px';
                $ratio      = $real_w / $real_h;
                $width_css  = round( $ratio * 90 ) . 'px';

            } else {
                // CASO C: Cuadrado -> Se queda en 84px (Standard WP)
                $width_css  = '84px';
                $height_css = '84px';
            }
        }

        return array( 'width' => $width_css, 'height' => $height_css );
    }
}