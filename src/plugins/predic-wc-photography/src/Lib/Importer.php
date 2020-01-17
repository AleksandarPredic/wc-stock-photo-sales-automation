<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterInterface;

/**
 * Class Importer
 *
 * @package PredicWCPhoto\Lib
 */
class Importer implements ImporterInterface
{

    /**
     * Importer terms class instance
     * @var \PredicWCPhoto\Contracts\ImporterTermInterface
     */
    private $importerTerms;

    /**
     * Instance of the class responsible to register taxonomies
     * @var WCTaxonomies
     */
    private $taxonomies;

    public function __construct()
    {
        $this->importerTerms = ImporterTermFactory::make();
        $this->taxonomies    = WCTaxonomies::getInstance();
    }

    /**
     * Return parent product id
     *
     * @param array $photos array ['filename' => string, 'path' => string]
     * @throws \Exception
     * @return array Return array of results for each photo [ 'filename' => ['id' => int, 'updated' => bool, 'children' => [int, int]] ... ]
     */
    public function import($photos)
    {
        // Onlu 10 photos allowed to avoid timeouts
        if (count($photos) > 10) {
            throw new \Exception(
                esc_html__('Maximum photos to process is 10. Please reload the page and select up to 10 photos.', 'predic-wc-photography'),
                403
            );
        }

        $result = [];

        foreach ($photos as $photo) {
            $filename            = str_replace('_', '-', strtolower(sanitize_file_name($photo['filename'])));
            $pathInfo            = pathinfo($filename);
            $fileExtension       = $pathInfo['extension'];
            $tmpName             = $photo['path'];
            $productSlug         = basename($filename, sprintf('.%s', $fileExtension));
            $wcProduct           = wc_get_product(wc_get_product_id_by_sku($productSlug)); // Check if product exists

            /**
             * Set vars from image metadata
             */
            $metaDataParser = ImporterImageMetaDataFactory::make();
            $metaDataParser->parse($tmpName);
            $camera          = $metaDataParser->getCamera();
            $resolution      = $metaDataParser->getResolution();
            $type            = $metaDataParser->getType();
            $productName     = ! empty($metaDataParser->getName()) ? $metaDataParser->getName() : ucfirst(str_replace('-', ' ', $productSlug));
            $description     = $metaDataParser->getDescription();
            $cameraUploadDate= $metaDataParser->getCameraUploadDate();
            $keywords        = $metaDataParser->getKeywords();
            $shootouts       = $metaDataParser->getShootout(); // Must be an array
            $models          = $metaDataParser->getModels(); // Must be an array

            /**
             * Parse tags ids form keywords
             */
            $productTagsIds = $this->parseTerms($keywords, 'product_tag');

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
             * Create products data
             */
            $wcImporter = new WCImporter(WCTaxonomies::getInstance());
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
                        'key'   => 'ps_camera',
                        'value' => sanitize_text_field($camera)
                    ],
                    [
                        'key'   => 'ps_resolution',
                        'value' => implode('x', array_map('sanitize_text_field', $resolution)) . 'px'
                    ],
                    [
                        'key'   => 'ps_type',
                        'value' => sanitize_text_field($type)
                    ],
                    [
                        'key'   => 'ps_camera_upload_date',
                        'value' => sanitize_text_field($cameraUploadDate)
                    ],
                ],
                // Add all custom taxonomies here [ [taxonomy_id => [array of ids] ] ]
                [
                    $this->taxonomies::SHOOTOUTS_ID => $shootouts,
                    $this->taxonomies::MODELS_ID    => $models,
                ]
            );

            /**
             * Import product
             *
             * Return is ['id' => int, 'updated' => bool, 'children' => [int, int]]
             */
            $variableProductArray   = $wcImporter->import();

            if (! isset($variableProductArray['children']) || count($variableProductArray['children']) < 2) {
                throw new \Exception(
                    sprintf(
                        esc_html__('Error. Parent product with id %s has no children set.', 'predic-wc-photography'),
                        $variableProductArray['id']
                    ),
                    500
                );
            }

            /**
             * Set download files for all children here
             */
            $importerDownloadFiles = ImporterDownloadFilesFactory::make();
            $importerDownloadFiles->import(
                $variableProductArray,
                $tmpName,
                $fileExtension,
                $productSlug
            );

            $result[$productSlug] = $variableProductArray;
        }

        return $result;
    }

	/**
	 * Import terms and create missing ones to a taxonomy
	 * @param array $terms Array of terms names, not slugs
	 * @param string $taxonomy Taxonomy to add terms to. Must exists
	 * @return array Return array of WP db ids of processed terms
	 */
    private function parseTerms($terms, $taxonomy)
    {
        $termsIds = [];
        if (empty($terms)) {
            return $termsIds;
        }

        foreach ($terms as $term) {
            try {
                $termsIds[] = $this->importerTerms->import($term, $taxonomy);
            } catch (\Exception $e) {
                // TODO: Add logger so we don't interrupt import process as this is not crucial
            }
        }

        return $termsIds;
    }
}
