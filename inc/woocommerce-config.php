<?php

add_filter('out_of_stock_add_to_cart_text','_td_change_out_of_stock_text');

add_filter('woocommerce_sale_flash','_td_change_sales_flash_text',10,3);

function _td_change_out_of_stock_text($text)
{
    return esc_html__("Нет в наличии",'tdev_smart_world');
}
function _td_change_sales_flash_text($text,$post,$product)
{
    return '<span class="onsale onsale-outter"><span class="onsale-inner">'.esc_html__( 'В наличии', 'tdev_smart_world' ).'</span></span>';
}