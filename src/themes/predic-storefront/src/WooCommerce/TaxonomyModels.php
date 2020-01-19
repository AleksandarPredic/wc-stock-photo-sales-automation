<?php

namespace PredicStorefront\WooCommerce;

use PredicStorefront\Traits\SingletonTrait;
use PredicWCPhoto\Lib\WCTaxonomies;

/**
 * Class TaxonomyModels displays related products from the same models on the single product page
 *
 * @package PredicStorefront\WooCommerce
 */
class TaxonomyModels extends TaxonmyFromPLugin
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
        $this->taxonomy     = $customTaxonomies::MODELS_ID;
        $this->templatePart = 'template-parts/woocommerce/models';
    }
}
