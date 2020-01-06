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

define( 'PREDIC-STOREFRONT_VERSION', '0.0.1' );

/**
 * Set autoloader
 */
require_once get_parent_theme_file_path( 'src/PredicStorefront/Autoloader.php');
\PredicStorefront\Autoloader::register();
