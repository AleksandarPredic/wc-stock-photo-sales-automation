<?php

namespace PredicWCPhoto\Helpers;

use PredicWCPhoto\Traits\SingletonTrait;

/**
 * Class PricesHelper
 *
 * @package PredicWCPhoto\Helpers
 */
class PricesHelper
{
    use SingletonTrait;

    /**
     * Validate if all values in the array are int values and that we have 2 values for regular and extended licences prices
     * Return first two items if array has more than 2 or false if only one value
     *
     * @param array $prices Expected input is array as prices [regular, extended]
     * @return array
     */
    public function validate($prices)
    {
        $default = [];
        if (empty($prices) || ! is_array($prices) || 2 > count($prices)) {
            return $default;
        }

        $valiatePrices = array_map(
            function ($value) {
                $value = intval($value);

                return is_int($value) && $value > 0 ? $value : false;
            },
        $prices);

        if (in_array(false, $valiatePrices)) {
            return $default;
        }

        return array_slice($valiatePrices, 0, 2);
    }
}
