<?php

define("CHILD_THEME_PATH_URI",get_stylesheet_directory_uri());
define("CHILD_THEME_PATH",get_stylesheet_directory());
define("CHILD_THEME_MAIN_STYLE",get_stylesheet_uri());
define("CHILD_THEME_UPLOAD_URI",wp_upload_dir()['baseurl']);

include('inc/rewrite_rules.php');

function bridge_qode_woocommerce_single_type() {
    $type = '';
    if (bridge_qode_is_woocommerce_installed()) {
        $type = bridge_qode_options()->getOptionValue('woo_product_single_type');
    }

    return $type;
}
include('framework/TDFramework.php');
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

    function bridge_qode_woo_qode_product_searchform($form) {

        $form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
			<div>
				<label class="screen-reader-text" for="s">' . esc_html__( 'Поиск:', 'bridge' ) . '</label>
				<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . esc_html__( 'Поиск продукта', 'bridge' ) . '" />
				<input type="submit" id="searchsubmit" value="&#xf002" />
				<input type="hidden" name="post_type" value="product" />
			</div>
		</form>';

        return $form;

    }
    add_filter( 'get_product_search_form' , 'bridge_qode_woo_qode_product_searchform' );

if (!function_exists('bridge_qode_woocommerce_content')){

    /**
     * Output WooCommerce content.
     *
     * This function is only used in the optional 'woocommerce.php' template
     * which people can add to their themes to add basic woocommerce support
     * without hooks or modifying core templates.
     *
     * @access public
     * @return void
     */
    function bridge_qode_woocommerce_content() {

        if ( is_singular( 'product' ) ) {

            while ( have_posts() ) : the_post();

                wc_get_template_part( 'content', 'single-product' );

            endwhile;

        } else {



            if ( have_posts() ) {

                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked wc_print_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action( 'woocommerce_before_shop_loop' );

                woocommerce_product_loop_start();

                if ( wc_get_loop_prop( 'total' ) ) {
                    while ( have_posts() ) {
                        the_post();

                        /**
                         * Hook: woocommerce_shop_loop.
                         *
                         * @hooked WC_Structured_Data::generate_product_data() - 10
                         */
                        do_action( 'woocommerce_shop_loop' );

                        wc_get_template_part( 'content', 'product' );
                    }
                }

                woocommerce_product_loop_end();

                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action( 'woocommerce_after_shop_loop' );


            } else {
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action( 'woocommerce_no_products_found' );
            }


            do_action( 'woocommerce_archive_description' );

        }
    }
}

if ( ! function_exists( 'woocommerce_taxonomy_archive_description' ) ) {

    /**
     * Show an archive description on taxonomy archives.
     */
    function woocommerce_taxonomy_archive_description() {
        if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
            $term = get_queried_object();

            if ( $term && ! empty( $term->description ) ) {
                echo '<div style="clear: both;"></div>';
                echo '<div class="term-description">' . wc_format_content( $term->description ) . '</div>'; // WPCS: XSS ok.
            }
        }
    }
}