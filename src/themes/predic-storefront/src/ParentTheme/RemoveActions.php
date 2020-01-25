<?php

namespace PredicStorefront\ParentTheme;

use PredicStorefront\Contracts\UsingHooksInterface;
use PredicStorefront\Traits\SingletonTrait;

/**
 * Class RemoveActions
 */
class RemoveActions implements UsingHooksInterface
{
    use SingletonTrait;

    /**
     * RemoveActions constructor.
     */
    private function __construct()
    {
    }

    /**
     * Add needed hooks
     */
    public function init()
    {
        add_action('template_redirect', [$this, 'customize']);
    }

    /**
     * Do your quick customization
     */
    public function customize()
    {
        // Remove sidebar
        remove_all_actions('storefront_sidebar');

        // Remove breadcrumbs as it display product categories which we don't use
        remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
    }
}
