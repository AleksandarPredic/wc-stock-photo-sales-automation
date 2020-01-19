<?php

namespace PredicWCPhoto\Controllers;

use PredicWCPhoto\Contracts\ControllerInterface;
use PredicWCPhoto\Data\Models\CustomizerModel;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class CustomizerController
 *
 * @package PredicWCPhoto\Controllers
 */
class CustomizerController implements ControllerInterface
{
    use  SingletonTrait;

    /**
     * Prices as string separated by ;
     * Pattern: regular; extended
     * Example 99; 1001
     *
     * @var string
     */
    private $pricesOption;

    /**
     * Model instance
     * @var CustomizerModel
     */
    private $model;

    /**
     * CustomizerController constructor.
     */
    private function __construct()
    {
        $this->model        = CustomizerModel::getInstance();
        $this->pricesOption = $this->model::PRICES_OPTION;
    }

    /**
     * Add hooks
     */
    public function init()
    {
        add_action('customize_register', [$this, 'register']);
    }

    /**
     * Register customizer settings
     * @param \WP_Customize_Manager $wp_customize
     */
    public function register($wp_customize)
    {
        /**
         * For more WooCommerce sections see\
         * wp-content/plugins/woocommerce/includes/customizer/class-wc-shop-customizer.php
         */

        /**
         * @see https://codex.wordpress.org/Class_Reference/WP_Customize_Manager/add_setting
         */
        $wp_customize->add_setting(
            $this->pricesOption,
            [
                'default'           => '',
                'type'              => 'option',
                'capability'        => 'manage_woocommerce',
                'sanitize_callback' => 'sanitize_text_field',
            ]
        );

        /**
         * @see https://developer.wordpress.org/reference/classes/wp_customize_manager/add_control/
         */
        $wp_customize->add_control(
            $this->pricesOption,
            [
                'label'       => esc_html__('Set fallback product prices. Example: 99; 1001', 'predic-wc-photography'),
                'description' => esc_html__('First price before ; is regular licence price. Second price is extended licence price.', 'predic-wc-photography'),
                'section'     => 'woocommerce_product_catalog',
                'settings'    => $this->pricesOption,
                'type'        => 'text'
            ]
        );
    }
}
