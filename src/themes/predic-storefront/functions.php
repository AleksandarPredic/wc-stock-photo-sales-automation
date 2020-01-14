<?php
/**
 * Predic Storefront child theme functions and definitions.
 *
 * @link       https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package    Predic Storefront child theme
 * @subpackage Templates
 * @since      0.0.1
 * @author     Aleksandar Predic
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

define( 'PREDIC_STOREFRONT_VERSION', '0.0.1' );



/**
 * Set autoloader
 */
require 'vendor/autoload.php';

/**
 * Config
 */
\PredicStorefront\Config::getInstance()->init();

add_action('template_redirect', function () {

	/* WC */
	remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

	/* Storefront */
	remove_all_actions( 'storefront_sidebar' );

	/* Remove unneeded post classes */

	// Make sure no one override this and add extra classes to .product
	add_filter('woocommerce_get_product_class_include_taxonomies', function ($bool){
		return false;
	}, 1, 999);

	add_filter('woocommerce_post_class', function ($classes, $product){
		return array(
			'product',
			'post-' . $product->get_id()
		);
	}, 1, 999);
});


