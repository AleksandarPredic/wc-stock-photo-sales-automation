<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterImagesInterface;

/**
 * Class ImporterImagesFactory
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterImagesFactory
{
    /**
     * Return images importer class instance
     *
     * @return ImporterImagesInterface
     */
    public static function make()
    {
        return new ImporterImages();
    }
}
