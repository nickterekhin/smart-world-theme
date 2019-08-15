<?php

define("CHILD_THEME_PATH_URI",get_stylesheet_directory_uri());
define("CHILD_THEME_PATH",get_stylesheet_directory());
define("CHILD_THEME_MAIN_STYLE",get_stylesheet_uri());
define("CHILD_THEME_UPLOAD_URI",wp_upload_dir()['baseurl']);

include('inc/woocommerce-config.php');


add_action('wp_enqueue_scripts', 'main_style_setup',20);
function main_style_setup()
{


    wp_register_style( 'td-fontawesome-css', CHILD_THEME_PATH_URI.'/content/css/all.css?13');
    wp_register_style( 'custom-css', CHILD_THEME_PATH_URI.'/content/css/custom.css?13');
    wp_register_script( 'custom-js', CHILD_THEME_PATH_URI . '/content/js/custom.js?13');


    wp_enqueue_style( 'td-fontawesome-css' );
    wp_enqueue_style( 'custom-css' );
    wp_enqueue_script( 'custom-js' );
}



//add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter('show_admin_bar', '__return_false');

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery-mask-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js', array(), '1.14.15', true);
});

add_filter('quform_element_valid_1_5', function ($valid, $value, Quform_Element_Field $element) {
    if ( ! preg_match('/^\(\d{3}\) \d{3}\-\d{4}$/', $value)) {
        $element->addError('Введите номер телефона в формате (000) 000-0000');
        $valid = false;
    }

    return $valid;
}, 10, 3);