<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @var array $attributes
 * @var array $args
 * @var array $prices
 * @var array $includeFields
 * @var string $title
 * @var string $formAction
 *
 * use instance['title'] to show widget title
 *
 * $attribute->display_type = '' - Default , 'scroll' - Scroll ,'dropdown' - Dropdown ,'scroll_dropdown' - Scroll + Dropdown
 * $attribute->has_checked = true, false
 * $attribute->html_type = 'select', 'color', 'label', 'radio'
 */

?>

<?php echo $args['before_widget']; ?>

<?php if (!empty($instance['title'])): ?>
    <?php echo $args['before_title'] . $instance['title'] . $args['after_title'] ?>
<?php endif; ?>

<div class="filter__chevron">
    <svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"></path>
    </svg>
</div>

<div class="filter filter--style-<?php echo $style ?>" data-premmerce-filter>


    <?php foreach ($attributes as $attribute): ?>
        <?php do_action('premmerce_filter_render_item_before', $attribute); ?>


        <div class="filter__item <?php echo 'filter__item--type-' . $attribute->html_type; ?>"
             data-premmerce-filter-drop-scope>

            <?php
            $dropdown = in_array($attribute->display_type, ['dropdown', 'scroll_dropdown']);
            $scroll = in_array($attribute->display_type, ['scroll', 'scroll_dropdown']);
            ?>

            <div class="filter__header" <?php echo $dropdown ? 'data-premmerce-filter-drop-handle' : ''; ?>>
                <div class="filter__title">
                    <?php echo apply_filters('premmerce_filter_render_item_title', $attribute->attribute_label,
                        $attribute) ?>
                </div>
                <?php do_action('premmerce_filter_render_item_after_title', $attribute); ?>
            </div>
            <div class="filter__inner <?php echo ($dropdown && !$attribute->has_checked) ? 'filter__inner--js-hidden' : ''; ?> <?php echo $scroll ? 'filter__inner--scroll' : ''; ?>"
                 data-premmerce-filter-inner <?php echo $scroll ? 'data-filter-scroll' : ''; ?>>

                <?php do_action('premmerce_filter_render_item_' . $attribute->html_type, $attribute); ?>

            </div>
        </div>
        <?php do_action('premmerce_filter_render_item_after', $attribute); ?>
    <?php endforeach ?>
</div>
<?php echo $args['after_widget']; ?>

