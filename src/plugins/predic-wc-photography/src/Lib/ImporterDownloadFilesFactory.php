<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterDownloadFilesInterface;

/**
 * Class ImporterDownloadFilesFactory
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterDownloadFilesFactory
{
    /**
     * Return importer for download files instance
     *
     * @return ImporterDownloadFilesInterface
     */
    public static function make()
    {
        return new ImporterDownloadFiles();
    }
}
