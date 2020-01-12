<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterDownloadFilesInterface;
use PredicWCPhoto\Traits\FilesTrait;

/**
 * Class ImporterDownloadFiles
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterDownloadFiles implements ImporterDownloadFilesInterface
{
    use FilesTrait;

    /**
     * Files permanent store path in uploads folder
     * @var string
     */
    private $filesUploadDirPath = 'pwcp-wc-downloadable-files';

    /**
     * This suffix will be added to downloadable files name
     * @var string
     */
    private $filesSuffix = '-download';

    /**
     * ImporterDownloadFiles constructor.
     */
    public function __construct()
    {
        // Set temporary folder to manipulate images
        $uploadDir                            = wp_upload_dir();
        $this->filesUploadDirPath             = $uploadDir['basedir'] . '/pwcp-wc-downloadable-files';
    }

    /**
     * @param array  $variableProductArray
     * @param string $imagePath
     * @param string $fileExtension
     * @param string $productSlug
     * @throws \Exception
     * @return bool
     */
    public function import($variableProductArray, $imagePath, $fileExtension, $productSlug)
    {
        if (! isset($variableProductArray['id']) || ! isset($variableProductArray['children'])) {
            throw new \Exception(
                sprintf(
                    esc_html__('Error $variableProductArray incomplete. File: %s.%s', 'predic-wc-photography'),
                    $productSlug,
                    $fileExtension
                ),
                400
            );
        }

        $variableProductId       = $variableProductArray['id'];
        $variableProductChildren = $variableProductArray['children'];

        // Make sure we have dir
        $this->mkDir($this->filesUploadDirPath);

        $wcProductDownload = new \WC_Product_Download();
        /**
         * @important Id must not be longer than 32 chars due to the WC bug https://github.com/woocommerce/woocommerce/issues/20412
         */
        $wcProductDownload->set_id($variableProductId . $this->filesSuffix);
        $wcProductDownload->set_name($productSlug . $this->filesSuffix);

        $filePathParentProductFolder = $this->filesUploadDirPath . '/' . $variableProductId;
        $filePath                    = $filePathParentProductFolder . '/' . $wcProductDownload->get_name() . '.' . $fileExtension;

        $this->mkDir($filePathParentProductFolder);
        $moved = copy($imagePath, $filePath);

        if (! $moved) {
            throw new \Exception(
                sprintf(
                    esc_html__('Error. Downloadable file not copied to destination %s.', 'predic-wc-photography'),
                    $filePath
                ),
                500
            );
        }

        $wcProductDownload->set_file($filePath);

        foreach ($variableProductChildren as $childId) {
            $childProduct = wc_get_product($childId);

            if (! is_a($childProduct, '\WC_Product_Variation')) {
                throw new \Exception(
                    sprintf(
                        esc_html__('Error assigning downloadable file to children products. Variable product id: %s.', 'predic-wc-photography'),
                        $filePath
                    ),
                    500
                );
            }

            $childProduct->set_downloads([$wcProductDownload]);
            $childProduct->save();

            //TODO:  Maybe validate if save sucessfull
        }

        return true;
    }
}
