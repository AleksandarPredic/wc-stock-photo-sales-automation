<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Traits\SingletonTrait;
use PredicWCPhoto\Lib\WCTaxonomies;

/**
 * Class TaxonomyShootout displays related products from the same shoot on the single product page
 *
 * @package PredicStorefront\WooCommerce
 */
class TaxonomyShootout extends TaxonmyFromPLugin
{
    use SingletonTrait;

    /**
     * TaxonomyModels constructor.
     */
    private function __construct()
    {
        if (! class_exists('PredicWCPhoto\Lib\WCTaxonomies')) {
            return;
        }

        parent::__construct();

        $customTaxonomies   = WCTaxonomies::getInstance();
        $this->taxonomy     = $customTaxonomies::SHOOTOUTS_ID;
        $this->templatePart = 'template-parts/woocommerce/shootout';
    }
}
