<?php

namespace PredicStorefront\ParentTheme;

use PredicStorefront\Contracts\UsingHooksInterface;
use PredicStorefront\Traits\SingletonTrait;

/**
 * Class ThemeOverrides
 *
 * @package PredicStorefront\ParentTheme
 */
class ThemeOverrides implements UsingHooksInterface
{
    use SingletonTrait;

    /**
     * ThemeOverrides constructor.
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
     * Do your customization
     */
    public function customize()
    {
        // Footer copyright modifications
		remove_action('storefront_footer', 'storefront_credit',20);
		add_action('storefront_footer', function () {
			printf(
				'<div class="site-info">%s<br />%s</div><!-- .site-info -->',
				esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ),
				wp_kses(
					sprintf(
						__('Built with heart by %s', 'predic-storefront'),
						'<a href="//acapredic.com" target="_blank" rel="nofollow">acapredic.com</a>'
					),
					['a' => ['href' => true, 'target' => true, 'rel' => true]]
				)
			);
		}, 20);


    }
}
