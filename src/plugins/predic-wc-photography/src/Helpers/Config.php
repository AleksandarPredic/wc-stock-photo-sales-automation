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
    private $pluginSlug;

    /**
     * Plugin directory path.
     *
     * @access private
     * @since  0.0.1
     * @var string
     */
    private $pluginDirPath;

    /**
     * Plugin directory url.
     *
     * @access private
     * @since  0.0.1
     * @var string
     */
    private $pluginDirUri;

    /**
     * Plugin basename.
     *
     * @access private
     * @since  0.0.1
     * @var string
     */
    private $pluginBasename;

    /**
     * Distribution styles and scripts url.
     *
     * @access private
     * @since  0.0.1
     * @var string
     */
    private $distributionUri;

    /**
     * Config constructor.
     *
     * @access public
     * @since  0.0.1
     *
     * @param string $pluginDirUrl  Plugin directory url.
     * @param string $pluginDirPath Plugin directory path.
     * @param string $pluginBasename Plugin basename.
     */
    public function __construct($pluginDirUrl, $pluginDirPath, $pluginBasename)
    {
        $this->version          = '0.0.1';
        $this->pluginSlug       = 'predic-wc-photography';
        $this->pluginDirUri     = $pluginDirUrl;
        $this->pluginDirPath    = $pluginDirPath;
        $this->pluginBasename   = $pluginBasename;
        $this->distributionUri  = $this->pluginDirUri . 'dist';
    }

    /**
     * Return plugin version.
     *
     * @access public
     * @since  0.0.1
     *
     * @return string
     */
    public function getPluginVersion()
    {
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
    public function getPluginSlug()
    {
        return $this->pluginSlug;
    }

    /**
     * Return plugin directory url.
     *
     * @access public
     * @since  0.0.1
     *
     * @return string
     */
    public function getPluginDirUri()
    {
        return $this->pluginDirUri;
    }

    /**
     * Return plugin directory path.
     *
     * @access public
     * @since  0.0.1
     *
     * @return string
     */
    public function getPluginDirPath()
    {
        return $this->pluginDirPath;
    }

    /**
     * Return plugin basename.
     *
     * @access public
     * @since  0.0.1
     *
     * @return string
     */
    public function getPluginBasename()
    {
        return $this->pluginBasename;
    }

    /**
     * Return plugin distribution styles and scripts url.
     *
     * @access public
     * @since  0.0.1
     *
     * @return string
     */
    public function getDistributionUri()
    {
        return $this->distributionUri;
    }
}
