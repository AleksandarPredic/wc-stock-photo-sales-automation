<?php
/**
 * Class for enqueue scripts and styles for Predic WooCommerce photography plugin.
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
 * Class Scripts
 *
 * @package PredicWCPhoto
 */
class Scripts
{
	/**
	 * Plugin version.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $version;

	/**
	 * Plugin distribution url.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $distribution_uri;

	/**
	 * Scripts constructor.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __construct() {
		$this->version          = predic_wc_photography_helpers()->config->get_plugin_version();
		$this->distribution_uri = predic_wc_photography_helpers()->config->get_distribution_uri();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1010 );
	}

	/**
	 * Enqueue plugin scripts
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function enqueue_scripts()
	{
		// Enqueue Styles
		wp_enqueue_style(
			'predic-wc-photography-css',
			$this->distribution_uri . '/css/main.css',
			array(),
			$this->version,
			'all'
		);

		// Enqueue Scripts
		wp_enqueue_script(
			'predic-wc-photography',
			$this->distribution_uri . '/js/main.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}
}
