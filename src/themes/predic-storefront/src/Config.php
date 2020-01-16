<?php

namespace PredicStorefront;

use PredicStorefront\Traits\SingletonTrait;

/**
 * Class Config
 *
 * @package PredicStorefront
 */
class Config
{
    use SingletonTrait;

    /**
     * Config constructor.
     */
    private function __construct()
    {
    }

    /**
     * Add needed hooks
     */
    public function init()
    {
        add_action('wp_enqueue_scripts', [$this, 'scripts'], 1000);
        add_action('after_setup_theme', [$this, 'afterThemeSetup']);
    }

    /**
     * Add and remove scripts and styles
     */
    public function scripts()
    {
        // Remove child unused root style css
        wp_dequeue_style('storefront-child-style');

        wp_enqueue_style('predic-storefront', get_stylesheet_directory_uri() . '/dist/assets/css/main.css', PREDIC_STOREFRONT_VERSION);
    }

	/**
	 * Add our custom image size
	 */
    public function afterThemeSetup()
    {
		add_image_size( 'ps-almost-full-hd', 1600 );
    }
}
