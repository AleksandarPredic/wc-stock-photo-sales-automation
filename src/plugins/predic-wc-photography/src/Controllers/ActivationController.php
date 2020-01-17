<?php

namespace PredicWCPhoto\Controllers;

use PredicWCPhoto\Lib\WCTaxonomies;
use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class ActivationController
 *
 * @package PredicWCPhoto\Controllers
 */
class ActivationController
{
    use SingletonTrait;

    /**
     * ActivationController constructor.
     */
    private function __construct()
    {
    }

    /**
     * Run on plugin activation
     */
    public function init()
    {
        WCTaxonomies::getInstance()->init();
        flush_rewrite_rules();
    }
}
