<?php

add_filter('out_of_stock_add_to_cart_text','_td_change_out_of_stock_text');

add_filter('woocommerce_sale_flash','_td_change_sales_flash_text',10,3);
add_filter('woocommerce_loop_add_to_cart_link','_td_add_to_cart',10,3);
add_filter('woocommerce_format_sale_price','_td_price_view',10,3);
add_filter('woocommerce_product_add_to_cart_text','_td_add_to_cart_text',10,2);
add_filter('woocommerce_product_single_add_to_cart_text','_td_add_to_cart_text');

add_filter('loop_shop_per_page', 'bridge_qode_woocommerce_products_per_page', 20);

if ( ! function_exists('bridge_qode_woocommerce_products_per_page') ) {
    /**
     * Function that sets number of products per page. Default is 9
     * @return int number of products to be shown per page
     */
    function bridge_qode_woocommerce_products_per_page()
    {
        return 30;
    }
}
function _td_change_out_of_stock_text($text)
{
    return esc_html__("Нет в наличии",'tdev_smart_world');
}
function _td_change_sales_flash_text($text,$post,$product)
{
    return '<span class="onsale onsale-outter"><span class="onsale-inner">'.esc_html__( 'В наличии', 'tdev_smart_world' ).'</span></span>';
}

/**
 * @var WC_Product_Variable $product
 * @return string
 */
function _td_add_to_cart($content,$product,$args)
{
    $add_to_cart = sprintf('<a href="%s" data-quantity="%s" class="add-to-cart-link %s"><i class="fa fa-plus"></i>%s</a>',esc_url( $product->add_to_cart_url() ),
        esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '', esc_html( $product->single_add_to_cart_text()));

    return $add_to_cart;
}

function _td_price_view($price,$regular_price,$sale_price)
{
    $price = '<ins>'.( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
 return $price;
}

function _td_add_to_cart_text()
{
    return 'В корзину';
}


function woocommerce_catalog_ordering() {
    if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
        return;
    }
    $show_default_orderby = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
    $catalog_orderby_options = apply_filters(
        'woocommerce_catalog_orderby',
        array(
            'menu_order' => __( 'По умолчанию', 'woocommerce' ),
            'popularity' => __( 'по популярности', 'woocommerce' ),
            'rating'     => __( 'По рейтингу', 'woocommerce' ),
            'date'       => __( 'Последние', 'woocommerce' ),
            'price'      => __( 'От дещевых к дорогим', 'woocommerce' ),
            'price-desc' => __( 'От дорогих к дешевым', 'woocommerce' ),
        )
    );

    $default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
    $orderby         = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

    if ( wc_get_loop_prop( 'is_search' ) ) {
        $catalog_orderby_options = array_merge( array( 'relevance' => __( 'Релевнтные', 'woocommerce' ) ), $catalog_orderby_options );

        unset( $catalog_orderby_options['menu_order'] );
    }

    if ( ! $show_default_orderby ) {
        unset( $catalog_orderby_options['menu_order'] );
    }

    if ( ! wc_review_ratings_enabled() ) {
        unset( $catalog_orderby_options['rating'] );
    }

    if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
        $orderby = current( array_keys( $catalog_orderby_options ) );
    }

    wc_get_template(
        'loop/orderby.php',
        array(
            'catalog_orderby_options' => $catalog_orderby_options,
            'orderby'                 => $orderby,
            'show_default_orderby'    => $show_default_orderby,
        )
    );
}

/////////////////////////////////////////////////////////
