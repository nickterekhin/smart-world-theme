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
        return 20;
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
