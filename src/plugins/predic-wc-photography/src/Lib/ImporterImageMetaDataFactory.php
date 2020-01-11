<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterImageMetaDataParserInterface;

/**
 * Class ImporterImageMetaDataFactory
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterImageMetaDataFactory
{

    /**
     * Return new metadata parser instance
     * @param string $imagePath Path to an image file
     * @return ImporterImageMetaDataParserInterface
     */
    public static function make($imagePath)
    {
        return new ImporterImageMetaDataParser($imagePath);
    }
}
