<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Contracts\CustomzeClassInterface;
use PredicStorefront\Traits\SingletonTrait;

/**
 * Class GlobalFilters
 *
 * @package PredicStorefront\WooCommerce
 */
class GlobalFilters implements CustomzeClassInterface
{
	use SingletonTrait;

	/**
	 * GlobalFilters constructor.
	 */
    private function __construct()
    {
    }

    /**
     * Add needed hooks
     */
    public function init()
    {
        add_action('template_redirect', [$this, 'customize']);
        add_action('after_setup_theme', [$this, 'customizeEarly']);
    }

    /**
     * Do your quick customization
     */
    public function customize()
    {
		/**
		 * Disable SKU usage
		 */
		add_filter( 'wc_product_sku_enabled', '__return_false' );

		/**
		 * Change add to cart button text
		 */
		$changeAddToCartText = function() {
			return esc_html__( 'Download', 'predic-storefront' );
		};
		add_filter( 'woocommerce_product_single_add_to_cart_text',  $changeAddToCartText);
		add_filter( 'woocommerce_product_add_to_cart_text',  $changeAddToCartText);
    }

    public function customizeEarly()
	{
		/**
		 * Set image dimensions and product grid columns and rows
		 *
		 * @see \Storefront_WooCommerce@setup
		 */
		add_filter('storefront_woocommerce_args', function ($args) {
			return array(
				'single_image_width'    => 1024,
				'thumbnail_image_width' => 330,
				'product_grid'          => array(
					'default_columns' => 4,
					'default_rows'    => 6,
					'min_columns'     => 1,
					'max_columns'     => 6,
					'min_rows'        => 1,
				),
			);
		});
	}
}
