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
	 *
     * @return ImporterImageMetaDataParserInterface
     */
    public static function make()
    {
        return new ImporterImageMetaDataParser();
    }
}
