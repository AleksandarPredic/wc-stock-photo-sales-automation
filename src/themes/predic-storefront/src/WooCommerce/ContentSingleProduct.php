<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Contracts\UsingHooksInterface;
use PredicStorefront\Traits\SingletonTrait;

/**
 * Class ContentSingleProduct
 *
 * @package PredicStorefront\WooCommerce
 */
class ContentSingleProduct implements UsingHooksInterface
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
        add_action('woocommerce_single_product_summary', [$this, 'imageMeta'], 40);

        /**
         * Hook: woocommerce_after_single_product_summary.
         */
        remove_all_actions('woocommerce_after_single_product_summary');

        /**
         * Hook: woocommerce_after_single_product
         */
        add_action('woocommerce_after_single_product', 'woocommerce_template_single_meta', 10);
        add_action('woocommerce_after_single_product', [TaxonomyShootout::getInstance(), 'render'], 20);
        add_action('woocommerce_after_single_product', [TaxonomyModels::getInstance(), 'render'], 30);

        /**
         * Modify single product image size for desktops
         */
        add_filter('woocommerce_gallery_image_size', function ($size) {
            return wp_is_mobile() ? $size : 'ps-almost-full-hd';
        }, 1, 20);

        /**
         * Add single product image class for aspect ration
         */
        add_filter('woocommerce_single_product_image_gallery_classes', function ($classes) {
            $product = wc_get_product(get_the_ID());
            $imageId = $product->get_image_id();

            if (empty($imageId)) {
                return $classes;
            }

            $imageMetadata = wp_get_attachment_metadata($imageId);

            if (empty($imageMetadata)) {
                return $classes;
            }

            $classes[] = $imageMetadata['width'] >= $imageMetadata['height'] ? 'ps-woocommerce-product-gallery--landscape' : 'ps-woocommerce-product-gallery--portrait';

            return $classes;
        }, 1, 20);
    }

    /**
     * Display image meta on single product page
     */
    public function imageMeta()
    {
        $meta = [
            'ps_camera'             => 'fa-camera',
            'ps_resolution'         => 'fa-expand-arrows-alt',
            'ps_type'               => 'far fa-image',
            'ps_camera_upload_date' => 'fa-calendar-alt'
        ];

        $productId = get_the_ID();

        $listItems = [];
        foreach ($meta as $metaKey => $icon) {
            $value = get_post_meta($productId, $metaKey, true);

            if (empty($value)) {
                continue;
            }

            $listItems[] = sprintf(
                '<li><i class="fas %1$s"></i><span>%2$s</span></li>',
                $icon,
                'ps_camera_upload_date' === $metaKey ? get_the_date(get_option('date_format')) : sanitize_text_field($value)
            );
        }

        printf(
            '<div class="ps-product-meta"><ul>%s</ul></div>',
            implode('', $listItems)
        );
    }
}
