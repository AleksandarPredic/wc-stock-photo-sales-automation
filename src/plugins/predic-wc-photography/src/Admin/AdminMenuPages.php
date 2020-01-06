<?php

namespace PredicWCPhoto\Admin;

use PredicWCPhoto\Traits\SingletonTrait;

class AdminMenuPages
{
    use SingletonTrait;

    /**
     * @var \PredicWCPhoto\Helpers\Init
     */
    private $config;

    /**
     * @var string
     */
    private $pluginSlug;

    private function __construct()
    {
        $this->config     = predic_wc_photography_helpers();
        $this->pluginSlug = $this->config->config->getPluginSlug();
    }

    public function build()
    {
        add_action('admin_menu', [$this, 'import']);
    }

    public function import()
    {
        add_submenu_page(
            'tools.php',
            esc_html__('Import photos to products', 'predic-wc-photography'),
            esc_html__('Import photos', 'predic-wc-photography'),
            'manage_options',
            sprintf('%s-import', $this->pluginSlug),
            [$this, 'render'],
            null
        );
    }

    public function render()
    {
        /**
         * Action hook predic_wc_photography_page_import
         */
        do_action(str_replace('-', '_', sprintf('%s_page_import', $this->pluginSlug)));
    }
}
