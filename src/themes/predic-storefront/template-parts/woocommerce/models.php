<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if (! defined('ABSPATH')) {
    exit;
}
$taxonomyModels = \PredicStorefront\WooCommerce\TaxonomyModels::getInstance();
$productsIds    = $taxonomyModels->getProductsIds();
$productTerms   = $taxonomyModels->getProductTerms();

if ($productsIds) : ?>

	<section class="ps-models">

		<?php
        $heading = apply_filters('ps_woocommerce_product_models_products_heading', esc_html(_n('More from this model', 'More from this models', count($productTerms), 'predic-storefront')));

        if ($heading) :
            ?><h2><?php echo esc_html($heading); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ($productsIds as $productId) : ?>

			<?php
            $post_object = get_post($productId);

            setup_postdata($GLOBALS['post'] =& $post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

            wc_get_template_part('content', 'product');
            ?>

		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
<?php
endif;

wp_reset_postdata();
