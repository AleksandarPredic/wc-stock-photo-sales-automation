<?php

namespace PredicWCPhoto\Contracts;

/**
 * Interface ImporterDownloadFilesInterface
 *
 * @package PredicWCPhoto\Contracts
 */
interface ImporterDownloadFilesInterface
{
    /**
     * Import original uploaded image and set download files for all children
     *
     * @param array  $variableProductArray ['id' => int, 'children' => [int, int]]
     * @param string $imagePath
     * @param string $fileExtension
     * @param string $productSlug
     * @throws \Exception
     * @return bool
     */
    public function import($variableProductArray, $imagePath, $fileExtension, $productSlug);
}
