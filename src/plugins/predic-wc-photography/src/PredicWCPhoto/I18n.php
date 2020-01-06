<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    Predic WooCommerce photography
 * @subpackage Core
 * @since      0.0.1
 * @author     Aleksandar Predic
 */

namespace PredicWCPhoto;

// Do not allow directly accessing this file.
if (! defined('ABSPATH')) {
	exit('Direct script access denied.');
}

/**
 * Class I18n
 *
 * @package PredicWCPhoto
 */
class I18n
{
	/**
	 * Plugin text domain specified for this plugin.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $text_domain;

	/**
	 * Plugin basename.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * I18n constructor.
	 *
	 * @aceess public
	 * @since  0.0.1
	 */
	public function __construct() {
		$this->text_domain     = predic_wc_photography_helpers()->config->get_plugin_slug();
		$this->plugin_basename = predic_wc_photography_helpers()->config->get_plugin_basename();
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->text_domain,
			false,
			$this->plugin_basename . '/languages'
		);
	}
}
