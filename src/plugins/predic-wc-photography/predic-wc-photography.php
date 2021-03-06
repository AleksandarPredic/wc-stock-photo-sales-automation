<?php
/**
 * Plugin Name: Predic WooCommerce photography
 * Plugin URI: https://acapredic.com
 * Description: Photography custom addon for WooCommerce
 * Version: 0.0.1
 * Author: Aleksandar Predic
 * Author URI: https://acapredic.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: predic-wc-photography
 * Domain Path: /languages
 */

use PredicWCPhoto\Controllers\ActivationController;

/**
 * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted or hOOK TO PRODUCT DELETE
 * Implementiraj js upload odavde https://www.dropzonejs.com/#usage
 * Instagram home footer
 * Prevod za temu poedit uraditi
 * Uradi npm run prod pred postavljanje
 * Na single product dodati request buyout
 * Add setting for enabling image optimizer
 */

// Do not allow directly accessing this file.
if (! defined('ABSPATH')) {
    exit('Direct script access denied.');
}

if (! class_exists('PredicWCPhoto_Plugin')) {
    // Setup class autoloader.
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

    // Load plugin.
    $predic_wc_photography_plugin = new \PredicWCPhoto\Plugin();
    add_action('plugins_loaded', [ $predic_wc_photography_plugin, 'load' ], 20);
	register_activation_hook( __FILE__, [ActivationController::getInstance(), 'init'] );

    if (! function_exists('predic_wc_photography_helpers')) {
        /**
         * Predic WooCommerce photography helper function.
         *
         * @since 0.0.1
         *
         * @return \PredicWCPhoto\Helpers\Init
         */
        function predic_wc_photography_helpers()
        {
            return  new \PredicWCPhoto\Helpers\Init(
                plugin_dir_url(__FILE__),
                plugin_dir_path(__FILE__),
                dirname(plugin_basename(__FILE__))
            );
        }
    }
}
