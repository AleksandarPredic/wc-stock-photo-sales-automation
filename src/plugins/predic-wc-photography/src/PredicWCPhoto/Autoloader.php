<?php
/**
 * Autoloader
 *
 * @package    Predic WooCommerce photography
 * @subpackage Core
 * @since      0.0.1
 * @author     Aleksandar Predic
 */

namespace PredicWCPhoto;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * Class Autoloader
 *
 * @package PredicWCPhoto
 */
class Autoloader {

	/**
	 * Handles autoloading of Predic WooCommerce photography classes.
	 *
	 * @since 0.0.1
	 *
	 * @param string $class_name
	 */
	function autoload( $class_name ) {

		// Check our namespace and prevent other classes from autoload
		if ( 0 !== strpos( $class_name, 'PredicWCPhoto' ) ) {
			return;
		}

		$fileName = wp_normalize_path( plugin_dir_path( dirname( __FILE__ ) ) . str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php' );

		if ( is_file( $fileName ) ) {
			require $fileName;
		}
	}

	/**
	 * Registers Predic WooCommerce photography Autoloader as an SPL autoloader.
	 *
	 * @since 0.0.1
	 *
	 * @param bool $prepend
	 */
	public static function register( $prepend = false ) {
		if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
			spl_autoload_register( array( new self(), 'autoload' ), true, $prepend );
		} else {
			spl_autoload_register( array( new self(), 'autoload' ) );
		}
	}
}
