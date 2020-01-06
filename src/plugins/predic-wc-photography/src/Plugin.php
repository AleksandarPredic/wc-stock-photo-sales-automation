<?php
/**
 * Predic WooCommerce photography plugin class.
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
 * Class Plugin
 *
 * @package PredicWCPhoto
 */
class Plugin
{

    /**
     * Flag to track if the plugin is loaded
     *
     * @since    0.0.1
     * @access   private
     * @var bool
     */
    private $loaded;

    /**
     * Constructor.
     *
     * @access public
     * @since 0.0.1
     */
    public function __construct()
    {
        $this->loaded      = false;
    }

    /**
     * Checks if the plugin is loaded.
     *
     * @access public
     * @since 0.0.1
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Loads the plugin into WordPress and add all needed hooks.
     *
     * @access public
     * @since 0.0.1
     */
    public function load()
    {
        if ($this->isLoaded()) {
            return;
        }

        // WooCommerce dependency
        if (! class_exists('WooCommerce')) {
            return;
        }

        /*
         * Add actions sorted via components we are adding trought plugin
         * All hooks are going to be added via class __construct method to make plugin modular
         */

        /**
         * Load textdomain.
         */
        $plugin_i18n = new I18n();
        add_action('init', [ $plugin_i18n, 'loadPluginTextdomain' ]);

        /**
         * Enqueue scripts and styles.
         */
        new Scripts();

        // Set all as loaded.
        $this->loaded = true;
    }

    /**
     * Fired during plugin activation.
     *
     * @access public
     * @since 0.0.1
     */
    public function activation()
    {
    }
}
