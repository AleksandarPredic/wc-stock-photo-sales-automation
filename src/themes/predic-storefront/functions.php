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

/**
 * WooCommerce
 */
\PredicStorefront\WooCommerce\ContentProduct::getInstance()->init();

/**
 * Parent theme
 */
\PredicStorefront\ParentTheme\RemoveActions::getInstance()->init();


