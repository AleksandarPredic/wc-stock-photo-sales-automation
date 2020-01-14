<?php

namespace PredicStorefront;

use PredicStorefront\Traits\SingletonTrait;

class Config
{
    use SingletonTrait;

    public function __construct()
    {
    }

    public function init()
    {
        add_action('wp_enqueue_scripts', [$this, 'scripts'], 1000);
    }

    public function scripts()
    {
        // Remove child unused root style css
        wp_dequeue_style('storefront-child-style');

        wp_enqueue_style('predic-storefront', get_stylesheet_directory_uri() . '/dist/assets/css/main.css', PREDIC_STOREFRONT_VERSION);
    }
}
