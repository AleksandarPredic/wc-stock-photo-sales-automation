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
    private $textDomain;

    /**
     * Plugin basename.
     *
     * @access private
     * @since  0.0.1
     * @var string
     */
    private $pluginBasename;

    /**
     * I18n constructor.
     *
     * @aceess public
     * @since  0.0.1
     */
    public function __construct()
    {
        $this->textDomain     = predic_wc_photography_helpers()->config->getPluginSlug();
        $this->pluginBasename = predic_wc_photography_helpers()->config->getPluginBasename();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @access public
     * @since  0.0.1
     */
    public function loadPluginTextdomain()
    {
        load_plugin_textdomain(
            $this->textDomain,
            false,
            $this->pluginBasename . '/languages'
        );
    }
}
