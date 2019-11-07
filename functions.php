<?php

define("CHILD_THEME_PATH_URI", get_stylesheet_directory_uri());
define("CHILD_THEME_PATH", get_stylesheet_directory());
define("CHILD_THEME_MAIN_STYLE", get_stylesheet_uri());
define("CHILD_THEME_UPLOAD_URI", wp_upload_dir()['baseurl']);

include('inc/rewrite_rules.php');

function bridge_qode_woocommerce_single_type()
{
    $type = '';
    if (bridge_qode_is_woocommerce_installed()) {
        $type = bridge_qode_options()->getOptionValue('woo_product_single_type');
    }

    return $type;
}

include('framework/TDFramework.php');
include('inc/woocommerce-config.php');


add_action('wp_enqueue_scripts', 'main_style_setup', 20);
function main_style_setup()
{


    wp_register_style('td-fontawesome-css', CHILD_THEME_PATH_URI . '/content/css/all.css?13');
    wp_register_style('custom-css', CHILD_THEME_PATH_URI . '/content/css/custom.css?13');
    wp_register_script('custom-js', CHILD_THEME_PATH_URI . '/content/js/custom.js?13');


    wp_enqueue_style('td-fontawesome-css');
    wp_enqueue_style('custom-css');
    wp_enqueue_script('custom-js');
}


//add_filter( 'use_block_editor_for_post', '__return_false' );
add_filter('show_admin_bar', '__return_false');

add_action('wp_enqueue_scripts', function () {
// todo: ask why it was here?
//    wp_enqueue_script('jquery-mask-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js', array(), '1.14.15', true);
});

add_filter('quform_element_valid_1_5', function ($valid, $value, Quform_Element_Field $element) {
    if (!preg_match('/^\(\d{3}\) \d{3}\-\d{4}$/', $value)) {
        $element->addError('Введите номер телефона в формате (000) 000-0000');
        $valid = false;
    }

    return $valid;
}, 10, 3);

function bridge_qode_woo_qode_product_searchform($form)
{

    $form = '<form role="search" method="get" id="searchform" action="' . esc_url(home_url('/')) . '">
			<div>
				<label class="screen-reader-text" for="s">' . esc_html__('Поиск:', 'bridge') . '</label>
				<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . esc_html__('Поиск продукта', 'bridge') . '" />
				<input type="submit" id="searchsubmit" value="&#xf002" />
				<input type="hidden" name="post_type" value="product" />
			</div>
		</form>';

    return $form;

}

add_filter('get_product_search_form', 'bridge_qode_woo_qode_product_searchform');

if (!function_exists('bridge_qode_woocommerce_content')) {

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
    function bridge_qode_woocommerce_content()
    {

        if (is_singular('product')) {

            while (have_posts()) : the_post();

                wc_get_template_part('content', 'single-product');

            endwhile;

        } else {


            if (have_posts()) {

                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked wc_print_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();

                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();

                        /**
                         * Hook: woocommerce_shop_loop.
                         *
                         * @hooked WC_Structured_Data::generate_product_data() - 10
                         */
                        do_action('woocommerce_shop_loop');

                        wc_get_template_part('content', 'product');
                    }
                }

                woocommerce_product_loop_end();

                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');


            } else {
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }


            do_action('woocommerce_archive_description');

        }
    }
}

if (!function_exists('woocommerce_taxonomy_archive_description')) {

    /**
     * Show an archive description on taxonomy archives.
     */
    function woocommerce_taxonomy_archive_description()
    {
        if (is_product_taxonomy() && 0 === absint(get_query_var('paged'))) {
            $term = get_queried_object();

            if ($term && !empty($term->description)) {
                echo '<div style="clear: both;"></div>';
                echo '<div class="term-description">' . wc_format_content($term->description) . '</div>'; // WPCS: XSS ok.
            }
        }
    }
}


if (!function_exists('bridge_qode_custom_breadcrumbs')) {
    function bridge_qode_custom_breadcrumbs()
    {

        global $post;
        $homeLink = esc_url(home_url('/'));
        $blogTitle = get_option('blogname');
        $pageid = bridge_qode_get_page_id();
        $bread_style = "";
        if (get_post_meta($pageid, "qode_page_breadcrumbs_color", true) != "") {
            $bread_style = " style='color:" . get_post_meta($pageid, "qode_page_breadcrumbs_color", true) . "';";
        }
        $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
        $delimiter_sign = bridge_qode_options()->getOptionValue('breadcrumbs_delimiter_sign');
        if (!empty($delimiter_sign)) {
            $delimiter = '<span class="delimiter"' . $bread_style . '>&nbsp;' . $delimiter_sign . '&nbsp;</span>'; // delimiter between crumbs inserted in Qode Options
        } else {
            $delimiter = '<span class="delimiter"' . $bread_style . '>&nbsp;>&nbsp;</span>'; // default delimiter between crumbs
        }
        $home = esc_html__('Главная', 'bridge'); // text for the 'Home' link
        $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
        $before = '<span class="current"' . $bread_style . '>'; // tag before the current crumb
        $after = '</span>'; // tag after the current crumb

        if (is_home() && !is_front_page()) {


            echo '<div class="breadcrumbs"><div itemprop="breadcrumb" class="breadcrumbs_inner"><a' . $bread_style . ' href="' . $homeLink . '">' . $home . '</a>' . $delimiter . ' <a' . $bread_style . ' href="' . $homeLink . '">' . get_the_title($pageid) . '</a></div></div>';

        } elseif (is_home()) {

            if ($showOnHome == 1) echo '<div class="breadcrumbs"><div itemprop="breadcrumb" class="breadcrumbs_inner"><a' . $bread_style . ' href="' . $homeLink . '">' . $home . '</a></div></div>';
        } elseif (is_front_page()) {

            if ($showOnHome == 1) echo '<div class="breadcrumbs"><div itemprop="breadcrumb" class="breadcrumbs_inner"><a' . $bread_style . ' href="' . $homeLink . '">' . $home . '</a></div></div>';
        } else {

            echo '<div class="breadcrumbs"><div itemprop="breadcrumb" class="breadcrumbs_inner"><a' . $bread_style . ' href="' . $homeLink . '">' . $home . '</a>' . $delimiter;


            if (is_category() || !bridge_qode_is_product_category()) {
                $thisCat = get_category(get_query_var('cat'), false);
                if (isset($thisCat->parent) && $thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter);
                echo bridge_qode_get_module_part($before . single_cat_title('', false) . $after);
            } elseif (is_category() || bridge_qode_is_product_category()) {
                $thisCat = get_category(get_queried_object(), false);
                if (isset($thisCat->parent) && $thisCat->parent != 0) echo get_term_parents_list($thisCat->parent, 'product_cat', array('separator' => ' ' . $delimiter, 'link' => true, 'format' => 'name'));

                echo bridge_qode_get_module_part($before . single_cat_title('', false) . $after);

            } elseif (is_search()) {
                echo bridge_qode_get_module_part($before . esc_html__('Search results for "', 'bridge') . get_search_query() . '"' . $after);

            } elseif (is_day()) {
                echo '<a' . $bread_style . ' href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter;
                echo '<a' . $bread_style . ' href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $delimiter;
                echo bridge_qode_get_module_part($before . get_the_time('d') . $after);

            } elseif (is_month()) {
                echo '<a' . $bread_style . ' href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter;
                echo bridge_qode_get_module_part($before . get_the_time('F') . $after);

            } elseif (is_year()) {
                echo bridge_qode_get_module_part($before . get_the_time('Y') . $after);

            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post' && get_post_type() != 'product') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    if ($showCurrent == 1) echo bridge_qode_get_module_part($before . get_the_title() . $after);
                } elseif (get_post_type() == 'product') {
                    $post_type = get_post_type_object(get_post_type());
                    $shop_page = get_option('woocommerce_shop_page_id');
                    if (!empty($shop_page)) {
                        echo '<a' . $bread_style . ' href="' . get_permalink($shop_page) . '">' . get_the_title($shop_page) . '</a>' . $delimiter;
                    }
                    if ($showCurrent == 1) echo bridge_qode_get_module_part($before . get_the_title() . $after);
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $cats = get_category_parents($cat, TRUE, ' ' . $delimiter);
                    if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
                    echo bridge_qode_get_module_part($cats);
                    if ($showCurrent == 1) echo bridge_qode_get_module_part($before . get_the_title() . $after);
                }

            } elseif (is_attachment() && !$post->post_parent) {
                if ($showCurrent == 1) echo bridge_qode_get_module_part($before . get_the_title() . $after);

            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                if ($cat) {
                    $cat = $cat[0];
                    echo get_category_parents($cat, TRUE, ' ' . $delimiter);
                }
                echo '<a' . $bread_style . ' href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
                if ($showCurrent == 1) echo bridge_qode_get_module_part($delimiter . $before . get_the_title() . $after);

            } elseif (is_page() && !$post->post_parent) {
                if ($showCurrent == 1) echo bridge_qode_get_module_part($before . get_the_title() . $after);

            } elseif (is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<a' . $bread_style . ' href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                for ($i = 0; $i < count($breadcrumbs); $i++) {
                    echo bridge_qode_get_module_part($breadcrumbs[$i]);
                    if ($i != count($breadcrumbs) - 1) echo ' ' . $delimiter;
                }
                if ($showCurrent == 1) echo bridge_qode_get_module_part($delimiter . $before . get_the_title() . $after);

            } elseif (is_tag()) {
                echo bridge_qode_get_module_part($before . esc_html__('Posts tagged "', 'bridge') . single_tag_title('', false) . '"' . $after);

            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo bridge_qode_get_module_part($before . esc_html__('Articles posted by ', 'bridge') . $userdata->display_name . $after);

            } elseif (is_404()) {
                echo bridge_qode_get_module_part($before . esc_html__('Error 404', 'bridge') . $after);
            } elseif (function_exists("is_woocommerce") && is_shop()) {

                if (get_option('woocommerce_shop_page_id')) {
                    echo bridge_qode_get_module_part($before . get_the_title(get_option('woocommerce_shop_page_id')) . $after);
                }
            }

            if (get_query_var('paged')) {

                echo bridge_qode_get_module_part($before . " (" . esc_html__('Page', 'bridge') . ' ' . get_query_var('paged') . ")" . $after);

            }

            echo '</div></div>';

        }
    }
}


function bridge_core_carousel($atts, $content = null)
{
    $args = array(
        "carousel"                => "",
        "number_of_visible_items" => "",
        "orderby"                 => "date",
        "order"                   => "ASC",
        "show_in_two_rows"        => ""
    );
    extract(shortcode_atts($args, $atts));

    $html = "";
    $carousel_holder_classes = "";
    if ($carousel != "") {

        if ($show_in_two_rows == 'yes') {
            $carousel_holder_classes = ' two_rows';
        }

        $visible_items = "";
        switch ($number_of_visible_items) {
            case 'four_items':
                $visible_items = 4;
                break;
            case 'five_items':
                $visible_items = 5;
                break;
            default:
                $visible_items = "";
                break;
        }

        $html .= "<div class='qode_carousels_holder clearfix" . $carousel_holder_classes . "'><div class='qode_carousels' data-number-of-visible-items='" . $visible_items . "'><ul class='slides'>";

        $q = array('post_type' => 'carousels', 'carousels_category' => $carousel, 'orderby' => $orderby, 'order' => $order, 'posts_per_page' => '-1');

        $query = new WP_Query($q);

        if ($query->have_posts()) : $postCount = 1;
            while ($query->have_posts()) : $query->the_post();

                if (get_post_meta(get_the_ID(), "qode_carousel-image", true) != "") {
                    $image = get_post_meta(get_the_ID(), "qode_carousel-image", true);
                } else {
                    $image = "";
                }

                if (get_post_meta(get_the_ID(), "qode_carousel-hover-image", true) != "") {
                    $hover_image = get_post_meta(get_the_ID(), "qode_carousel-hover-image", true);
                    $has_hover_image = "has_hover_image";
                } else {
                    $hover_image = "";
                    $has_hover_image = "";
                }

                if (get_post_meta(get_the_ID(), "qode_carousel-item-link", true) != "") {
                    $link = get_post_meta(get_the_ID(), "qode_carousel-item-link", true);
                } else {
                    $link = "";
                }

                if (get_post_meta(get_the_ID(), "qode_carousel-item-target", true) != "") {
                    $target = get_post_meta(get_the_ID(), "qode_carousel-item-target", true);
                } else {
                    $target = "_self";
                }

                $title = get_the_title();

                //is current item not on even position in array and two rows option is chosen?
                if ($postCount % 2 !== 0 && $show_in_two_rows == 'yes') {
                    $html .= "<li class='item'>";
                } elseif ($show_in_two_rows == '') {
                    $html .= "<li class='item'>";
                }
                $html .= '<div class="carousel_item_holder">';
//                if ($link != "") {
                $html .= "<a itemprop='url' href='" . $image . "' target='" . $target . "'>";
//                }

                $first_image = bridge_qode_get_attachment_id_from_url($image);

                if ($image != "") {
                    $html .= "<span class='first_image_holder " . $has_hover_image . "'>";

                    if (is_int($first_image)) {
                        $html .= wp_get_attachment_image($first_image, 'full');
                    } else {
                        $html .= '<img itemprop="image" src="' . $image . '" alt="' . esc_html__('carousel image', 'bridge') . '" />';
                    }


                    $html .= "</span>";
                }

                $second_image = bridge_qode_get_attachment_id_from_url($hover_image);

                if ($hover_image != "") {
                    $html .= "<span class='second_image_holder " . $has_hover_image . "'>";

                    if (is_int($second_image)) {
                        $html .= wp_get_attachment_image($second_image, 'full');
                    } else {
                        $html .= '<img itemprop="image" src="' . $hover_image . '" alt="' . esc_html__('carousel image', 'bridge') . '" />';
                    }


                    $html .= "</span>";
                }

//                if ($link != "") {
                $html .= "</a>";
//                }

                $html .= '</div>';

                //is current item on even position in array and two rows option is chosen?
                if ($postCount % 2 == 0 && $show_in_two_rows == 'yes') {
                    $html .= "</li>";
                } elseif ($show_in_two_rows == '') {
                    $html .= "</li>";
                }

                $postCount++;

            endwhile;

        else:
            $html .= esc_html__('Sorry, no posts matched your criteria.', 'bridge-core');
        endif;

        wp_reset_postdata();

        $html .= "</ul>";
        $html .= "</div></div>";

    }

    return $html;
}

add_shortcode('qode_carousel', 'bridge_core_carousel');
