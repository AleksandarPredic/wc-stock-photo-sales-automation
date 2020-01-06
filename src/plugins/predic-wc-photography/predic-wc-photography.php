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

// Do not allow directly accessing this file.
if (! defined('ABSPATH')) {
    exit('Direct script access denied.');
}

if (! class_exists('PredicWCPhoto_Plugin')) {
    // Setup class autoloader.
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
    \PredicWCPhoto\Autoloader::register();

    // Load plugin.
    $predic_wc_photography_plugin = new \PredicWCPhoto\Plugin();
    add_action('plugins_loaded', [ $predic_wc_photography_plugin, 'load' ], 1);

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
