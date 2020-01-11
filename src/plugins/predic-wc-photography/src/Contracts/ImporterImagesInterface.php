<?php

namespace PredicWCPhoto\Contracts;

/**
 * Interface ImporterImagesInterface
 *
 * @package PredicWCPhoto\Contracts
 */
interface ImporterImagesInterface
{
    /**
     * Import image
     *
     * @param string $filename Filename
     * @param string $imgPath Image path
     * @param string $caption Image caption
     * @param bool|\WC_Product $product
     * @return int Image id in WP db
     */
    public function import($filename, $imgPath, $caption = '', $product = false);
}
