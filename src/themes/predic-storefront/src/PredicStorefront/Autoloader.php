<?php
/**
 * Autoloader class for Predic Storefront child theme theme.
 *
 * @package    PredicStorefront
 * @subpackage Core
 * @since      0.0.1
 * @author     Aleksandar Predic
 */

namespace PredicStorefront;

/**
 * Class Autoloader
 *
 * @package PredicStorefront
 */
class Autoloader {

	/**
	 * Handles autoloading of Predic Storefront child theme classes.
	 *
	 * @since 0.0.1
	 * @access public
	 * @param string $class_name
	 */
	public function autoload( $class_name ) {

		// Check our namespace and prevent other classes from autoload
		if ( 0 !== strpos( $class_name, 'PredicStorefront' ) ) {
			return;
		}

		$fileName = wp_normalize_path(
			sprintf(
				'%s/%s.php',
				dirname( dirname( __FILE__ ) ),
				str_replace( '_', DIRECTORY_SEPARATOR, $class_name )
			)
		);

		if ( is_file( $fileName ) ) {
			require $fileName;
		}

	}

	/**
	 * Registers PredicStorefront_Autoloader as an SPL autoloader.
	 *
	 * @since 0.0.1
	 * @access public
	 * @param bool $prepend
	 *
	 * @throws \Exception
	 */
	public static function register( $prepend = false ) {

		if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
			spl_autoload_register( array( new self(), 'autoload' ), true, $prepend );
		} else {
			spl_autoload_register( array( new self(), 'autoload' ) );
		}

	}
}
