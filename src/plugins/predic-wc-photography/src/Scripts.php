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
if (! defined('ABSPATH')) {
    exit('Direct script access denied.');
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
    private $distributionUri;

    /**
     * Scripts constructor.
     *
     * @access public
     * @since  0.0.1
     */
    public function __construct()
    {
        $this->version          = predic_wc_photography_helpers()->config->getPluginVersion();
        $this->distributionUri  = predic_wc_photography_helpers()->config->getDistributionUri();

        add_action('wp_enqueue_scripts', [ $this, 'enqueueScripts' ], 1010);
    }

    /**
     * Enqueue plugin scripts
     *
     * @access public
     * @since  0.0.1
     */
    public function enqueueScripts()
    {

        // TODO: Setup scripts or remove it if not needed

        return;

        // Enqueue Styles
        wp_enqueue_style(
            'predic-wc-photography-css',
            $this->distributionUri . '/css/main.css',
            [],
            $this->version,
            'all'
        );

        // Enqueue Scripts
        wp_enqueue_script(
            'predic-wc-photography',
            $this->distributionUri . '/js/main.js',
            [ 'jquery' ],
            $this->version,
            true
        );
    }
}
