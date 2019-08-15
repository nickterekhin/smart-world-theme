<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
$bridge_qode_options = bridge_qode_return_global_options();

?>
<?php if ( version_compare( WOOCOMMERCE_VERSION, '3.4' ) >= 0 ) { ?>
<li <?php wc_product_class( '', $product ); ?>>
    <?php }
    else { ?>
<li <?php post_class(); ?>>
    <?php } ?>
    <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
    <div class="product-item-container">

    <div class="top-product-section">

        <a itemprop="url" href="<?php the_permalink(); ?>" class="product-category">
            <span class="image-wrapper">
            <?php
            /**
             * woocommerce_before_shop_loop_item_title hook
             *
             * @hooked woocommerce_show_product_loop_sale_flash - 10
             * @hooked woocommerce_template_loop_product_thumbnail - 10
             */
            do_action( 'woocommerce_before_shop_loop_item_title' );
            ?>
            </span>
        </a>



    </div>
    <a itemprop="url" href="<?php the_permalink(); ?>" class="product-category product-info">
        <h6 itemprop="name"><?php the_title(); ?></h6>

        <?php if(isset($bridge_qode_options['woo_products_show_title_sep']) && $bridge_qode_options['woo_products_show_title_sep'] == 'yes') { ?>
            <div class="separator after-title-spearator small center"></div>
        <?php } ?>


    </a>
    <footer>
            <?php
            /**
             * woocommerce_after_shop_loop_item_title hook
             *
             * @hooked woocommerce_template_loop_rating - 5
             * @hooked woocommerce_template_loop_price - 10
             */
            do_action( 'woocommerce_after_shop_loop_item_title' );

            ?>


            <?php do_action('bridge_qode_action_woocommerce_after_product_image'); ?>

    </footer>
    </div>
</li>