<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterTermInterface;

/**
 * Class ImporterTermFactory
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterTermFactory
{
    /**
     * Return terms importer instance
     * @return ImporterTermInterface
     */
    public static function make()
    {
        return ImporterTerm::getInstance();
    }
}
