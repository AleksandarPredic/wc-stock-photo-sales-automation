<?php

namespace PredicStorefront\ParentTheme;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class RemoveActions
 */
class RemoveActions
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
		/* Remove sidebar */
		remove_all_actions( 'storefront_sidebar' );
    }
}
