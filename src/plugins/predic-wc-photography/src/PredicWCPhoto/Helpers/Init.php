<?php
/**
 * Main Helper class for Predic WooCommerce photography plugin.
 *
 * @package    Predic WooCommerce photography
 * @subpackage Core
 * @since      0.0.1
 * @author     Aleksandar Predic
 */

namespace PredicWCPhoto\Helpers;

// Do not allow directly accessing this file.
if (! defined('ABSPATH')) {
	exit('Direct script access denied.');
}

/**
 * Class Init
 *
 * @package PredicWCPhoto\Helpers
 */
class Init
{

	/**
	 * Config instance.
	 *
	 * @access public
	 * @since  0.0.1
	 * @var \PredicWCPhoto\Helpers\Config
	 */
	public $config;

	/**
	 * Init constructor.
	 *
	 * @access public
	 * @param string $plugin_dir_url  Plugin directory url.
	 * @param string $plugin_dir_path Plugin directory path.
	 * @param string $plugin_basename Plugin basename.
	 * @since  0.0.1
	 *
	 */
	public function __construct($plugin_dir_url, $plugin_dir_path, $plugin_basename)
	{
		$this->config = new Config($plugin_dir_url, $plugin_dir_path, $plugin_basename);
	}
}
