<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Contracts\CustomzeClassInterface;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class ContentProduct
 *
 * @package PredicStorefront\WooCommerce
 */
class ContentProduct implements CustomzeClassInterface
{
    use SingletonTrait;

    /**
     * ContentProduct constructor.
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
        // WC
        remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

        // Remove unneeded post classes

        // Make sure no one override this and add extra classes to .product
        add_filter('woocommerce_get_product_class_include_taxonomies', function ($bool) {
            return false;
        }, 1, 999);

        add_filter('woocommerce_post_class', function ($classes, $product) {
            return [
                'product',
                'post-' . $product->get_id()
            ];
        }, 1, 999);
    }
}
