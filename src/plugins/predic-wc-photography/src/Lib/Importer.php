<?php

namespace PredicWCPhoto\Lib;

use Intervention\Image\ImageManagerStatic as Image;
use PredicWCPhoto\Contracts\ImporterInterface;

class Importer implements ImporterInterface
{
    /**
     * @var string
     */
    private $downloadableFilesUploadDirPath = 'pwcp-wc-downloadable-files';

    /**
     * Add two new taxonomies for model and shootout
     * Parse metadata - vidi ispod sta je jos ostalo
     * Odakle ce da se upisuje price za regular i extended
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted or hOOK TO PRODUCT DELETE
     * Javi djoletu da na nekim slikama nema podesen model aparata u metadata
     * Da se prikaze neki opis ispod stranice kategorije i da moze djole da tu upisuje html
	 *
	 * Create helper for creating directories
     */

    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function import($photos)
    {
        if (count($photos) > 10) {
            throw new \Exception(
                esc_html__('Maximum photos to process is 10. Please reload the page and select up to 10 photos.', 'predic-wc-photography'),
                403
            );
        }

        $result = [];

        // Set temporary folder to manipulate images
        $uploadDir                      = wp_upload_dir();
        $downloadableFilesUploadDirPath = $uploadDir['basedir'] . '/' . sanitize_file_name($this->downloadableFilesUploadDirPath);

        if (!file_exists($downloadableFilesUploadDirPath)) {
			$bool = mkdir($downloadableFilesUploadDirPath, 0755, true);

			if (! $bool) {
				throw new \Exception(
					sprintf(
						esc_html__('Error. Could not create dir: %s.', 'predic-wc-photography'),
						$this->tmpUploadDirPath
					),
					500
				);
			}
        }

        $importerTerms = ImporterTermFactory::make();

        foreach ($photos as $photo) {
            $filename            = str_replace('_', '-', strtolower(sanitize_file_name($photo['filename'])));
            $pathInfo            = pathinfo($filename);
            $fileExtension = $pathInfo['extension'];
            $tmpName             = $photo['tmp_name'];
            $productSlug         = basename($filename, sprintf('.%s', $fileExtension));
            $wcProduct           = wc_get_product(wc_get_product_id_by_sku($productSlug)); // Check if product exists

            /**
             * Set vars from image metadata
             */
            $metaDataParser = ImporterImageMetaDataFactory::make();
            $metaDataParser->parse($tmpName);
            $camera               = $metaDataParser->getCamera();
            $resolution           = $metaDataParser->getResolution();
            $type                 = $metaDataParser->getType();
            $productName          = ! empty($metaDataParser->getName()) ? $metaDataParser->getName() : ucfirst(str_replace('-', ' ', $productSlug));
            $description          = $metaDataParser->getDescription();
            $cameraUploadDate     = $metaDataParser->getCameraUploadDate();
            $terms                = $metaDataParser->getKeywords();

            /**
             * Parse tags ids form keywords
             */
            $productTagsIds = [];
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    try {
                        $productTagsIds[] = $importerTerms->import($term, 'product_tag');
                    } catch (\Exception $e) {
                        // TODO: Add logger so we don't interrupt import process as this is not crucial
                    }
                }
            }

            /**
             * Handle image manipulation
             */
            $imagesImporter = ImporterImagesFactory::make();
            $imageId        = $imagesImporter->import(
                $filename,
                $tmpName,
                $description,
                $wcProduct
            );

            /**
             * Create products first before any image manipulation
             */
            $wcImporter = new WCImporter();
            $wcImporter->setData(
                $productName,
                $productSlug,
                '',
                $description,
                [
                    99, // Regular price
                    999 // Extended price
                ],
                $imageId,
                $productTagsIds,
                [
                    [
                        'key'   => 'camera',
                        'value' => sanitize_text_field($camera)
                    ],
                    [
                        'key'   => 'resolution',
                        'value' => implode('x', array_map('sanitize_text_field', $resolution)) . 'px'
                    ],
                    [
                        'key'   => 'type',
                        'value' => sanitize_text_field($type)
                    ],
                    [
                        'key'   => 'camera_upload_date',
                        'value' => sanitize_text_field($cameraUploadDate)
                    ],
                ]
            );

            // Unhandled exception
            $parentProduct   = $wcImporter->import();
            $parentProductId = $parentProduct['id'];

            if (! isset($parentProduct['children']) || count($parentProduct['children']) < 2) {
                throw new \Exception(
                    sprintf(
                        esc_html__('Error. Parent product with id %s has no children.', 'predic-wc-photography'),
                        $parentProductId
                    ),
                    500
                );
            }

            /**
             * Set download files for all children here
             *
             * // TODO: Move this to separate importer class
             */
            $wcProductDownload = new \WC_Product_Download();

            /**
             * @important Id must not be longer than 32 chars due to the WC bug https://github.com/woocommerce/woocommerce/issues/20412
             */
            $wcProductDownload->set_id($parentProductId . '-download');
            $wcProductDownload->set_name($productSlug . '-download');

            $downloadableFilePathParentProductFolder = $downloadableFilesUploadDirPath . '/' . $parentProductId;
            $downloadableFilePath                    = $downloadableFilePathParentProductFolder . '/' . $wcProductDownload->get_name() . '.' . $fileExtension;

            if (!file_exists($downloadableFilePathParentProductFolder)) {
                mkdir($downloadableFilePathParentProductFolder, 0755, true);
            }

            $moved = copy($tmpName, $downloadableFilePath);

            if (! $moved) {
                throw new \Exception(
                    sprintf(
                        esc_html__('Error. Downloadable file not copied to destination %s.', 'predic-wc-photography'),
                        $downloadableFilePath
                    ),
                    500
                );
            }

            $wcProductDownload->set_file($downloadableFilePath);

            foreach ($parentProduct['children'] as $childId) {
                $childProduct = wc_get_product($childId);
                $childProduct->set_downloads([$wcProductDownload]);
                $childProduct->save();
                //TODO:  Maybe validate if save sucessfull
            }

            // Set feedback for all
            $result[$productSlug] = $parentProduct;
        }

        var_dump($result);
        die();
    }
}
