<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Contracts\CustomzeClassInterface;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class ContentSingleProduct
 *
 * @package PredicStorefront\WooCommerce
 */
class ContentSingleProduct implements CustomzeClassInterface
{
    use SingletonTrait;

	/**
	 * ContentSingleProduct constructor.
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
    }

    /**
     * Do your quick customization
     */
    public function customize()
    {
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 */
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

        /**
		 * Hook: woocommerce_after_single_product_summary.
		 */
        remove_all_actions('woocommerce_after_single_product_summary');
        add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 10);
    }
}
