<?php

namespace PredicWCPhoto\Data\Models;

use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class CustomizerModel
 *
 * @package PredicWCPhoto\Data\Models
 */
class CustomizerModel
{
    use SingletonTrait;

    /**
     * Prices as string separated by ;
     * Pattern: regular; extended
     * Example 99; 1001
     *
     * @var string
     */
    public const PRICES_OPTION = 'pwcp_product_global_prices';

    /**
     * Return prices as string separated by ;
     * Pattern: regular; extended
     * Example 99; 1001
     *
     * @@return  string|false
     */
    public function getPrices()
    {
        return get_option(self::PRICES_OPTION, false);
    }
}
