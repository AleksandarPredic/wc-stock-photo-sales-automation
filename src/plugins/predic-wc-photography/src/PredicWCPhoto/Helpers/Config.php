<?php
/**
 * Helper Config class for Predic WooCommerce photography plugin.
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

namespace PredicWCPhoto\Helpers;

/**
 * Class Config
 *
 * @package PredicWCPhoto\Helpers
 */
class Config
{

	/**
	 * Plugin version.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var string
	 */
	private $version;

	/**
	 * Plugin slug or text domain.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Plugin directory path.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $plugin_dir_path;

	/**
	 * Plugin directory url.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $plugin_dir_uri;

	/**
	 * Plugin basename.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * Distribution styles and scripts url.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var string
	 */
	private $distribution_uri;

	/**
	 * Config constructor.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @param string $plugin_dir_url  Plugin directory url.
	 * @param string $plugin_dir_path Plugin directory path.
	 * @param string $plugin_basename Plugin basename.
	 */
	public function __construct( $plugin_dir_url, $plugin_dir_path, $plugin_basename ) {
		$this->version          = '0.0.1';
		$this->plugin_slug      = 'predic-wc-photography';
		$this->plugin_dir_uri   = $plugin_dir_url;
		$this->plugin_dir_path  = $plugin_dir_path;
		$this->plugin_basename  = $plugin_basename;
		$this->distribution_uri = $this->plugin_dir_uri . 'dist';
	}

	/**
	 * Return plugin version.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->version;
	}

	/**
	 * Return plugin slug.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return plugin directory url.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_plugin_dir_uri() {
		return $this->plugin_dir_uri;
	}

	/**
	 * Return plugin directory path.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_plugin_dir_path() {
		return $this->plugin_dir_path;
	}

	/**
	 * Return plugin basename.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}

	/**
	 * Return plugin distribution styles and scripts url.
	 *
	 * @access public
	 * @since  0.0.1
	 *
	 * @return string
	 */
	public function get_distribution_uri() {
		return $this->distribution_uri;
	}
}
