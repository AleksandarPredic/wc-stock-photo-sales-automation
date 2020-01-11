<?php

namespace PredicWCPhoto\Lib;

use Intervention\Image\ImageManagerStatic as Image;
use PredicWCPhoto\Contracts\ImporterInterface;
use Spatie\ImageOptimizer\OptimizerChainFactory as ImageOptimizer;

class Importer implements ImporterInterface
{
    const IMAGE_MAX_WIDTH = 1920;
    const IMAGE_QUALITY   = 80;
    // TODO: Do all of this next

    /**
     * @var string
     */
    private $watermarkImagePath;

    /**
     * @var string
     */
    private $pluginSlug;

    /**
     * @var string
     */
    private $downloadableFilesUploadDirPath = 'pwcp-woocommerce-downloadable-files';

    /**
     * Add two new taxonomies for model and shootout
     * Parse metadata - vidi ispod sta je jos ostalo
     * Odakle ce da se upisuje price za regular i extended
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted or hOOK TO PRODUCT DELETE
     * Javi djoletu da na nekim slikama nema podesen model aparata u metadata
     * Da se prikaze neki opis ispod stranice kategorije i da moze djole da tu upisuje html
     */

    public function __construct()
    {
        $config                   = predic_wc_photography_helpers()->config;
        $this->watermarkImagePath = $config->getPluginImagesPath() . '/importer/watermark.png';
        $this->pluginSlug         = $config->getPluginSlug();
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
        $tmpUploadDirPath               = $uploadDir['basedir'] . '/' . sanitize_file_name($this->pluginSlug) . '-tmp';
        $downloadableFilesUploadDirPath = $uploadDir['basedir'] . '/' . sanitize_file_name($this->downloadableFilesUploadDirPath);

        if (!file_exists($tmpUploadDirPath)) {
            mkdir($tmpUploadDirPath, 0755, true);
        }

        if (!file_exists($downloadableFilesUploadDirPath)) {
            mkdir($downloadableFilesUploadDirPath, 0755, true);
        }

        $importerTerms = ImporterTermFactory::make();

        foreach ($photos as $photo) {
            $filename            = str_replace('_', '-', strtolower(sanitize_file_name($photo['filename'])));
            $pathInfo            = pathinfo($filename);
            $tmpUploadPath       = $photo['tmp_name'];
            $tmpFilePath         = $tmpUploadDirPath . '/' . $filename;
            $productSlug         = basename($filename, sprintf('.%s', $pathInfo['extension']));
            $wcProduct           = wc_get_product(wc_get_product_id_by_sku($productSlug)); // Check if product exists
            $wcProductImageId    = is_a($wcProduct, '\WC_Product') ? $wcProduct->get_image_id() : false;

            /**
             * Set vars from image metadata
             */
			$metaDataParser = ImporterImageMetaDataFactory::make($tmpUploadPath);
			$metaDataParser->parse();
            $camera          = $metaDataParser->getCamera();
            $resolution      = $metaDataParser->getResolution();
            $type     = $metaDataParser->getType();
            $productName          = ! empty($metaDataParser->getName()) ? $metaDataParser->getName() : ucfirst(str_replace('-', ' ', $productSlug));
            $description          = $metaDataParser->getDescription();
            $cameraUploadDate     = $metaDataParser->getCameraUploadDate();
            $terms      = $metaDataParser->getKeywords();

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
            // Delete image attached and re upload new so we can update new metadata
            if (! empty($wcProductImageId)) {
                $bool = false !== wp_delete_post($wcProductImageId, true);

                if (! $bool) {
                    throw new \Exception(
                        sprintf(
                            esc_html__('Error deleting product image with id %s.', 'predic-wc-photography'),
                            $this->product->get_id()
                        ),
                        500
                    );
                }
            }

            // Delete previous created file if doing this second time and file exists
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }

            // open an image file
            /**
             * https://packagist.org/packages/intervention/image
             */
            $img = Image::make($tmpUploadPath);

            /**
             * Process image before product
             */

            // Resize
            $img = $this->resizeImg($img);

            // insert a watermark - Other position: bottom-left
            $img->insert(Image::make($this->watermarkImagePath), 'center');

            // save image in desired format
            $img->save($tmpFilePath, self::IMAGE_QUALITY);

            // Optimize images
            /**
             * TODO: Make sure server has installed or nothing will happen
             *
             * apt-get install jpegoptim
             *
             * https://packagist.org/packages/spatie/image-optimizer
             */
            $optimizerChain = ImageOptimizer::create();
            $optimizerChain->optimize($tmpFilePath);

            $fileArray = [
                'name'     => $filename,
                'tmp_name' => $tmpFilePath,
            ];

            // https://wordpress.stackexchange.com/questions/70573/checking-if-a-file-is-already-in-the-media-library
            $imgaeId = media_handle_sideload(
                $fileArray,
                null,
                $description
            );

            // Delete tmp file, if media_handle_sideload didn't deleted it
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }

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
                $imgaeId,
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
            $downloadableFilePath                    = $downloadableFilePathParentProductFolder . '/' . $wcProductDownload->get_name() . '.' . $img->extension;

            if (!file_exists($downloadableFilePathParentProductFolder)) {
                mkdir($downloadableFilePathParentProductFolder, 0755, true);
            }

            $moved = copy($tmpUploadPath, $downloadableFilePath);

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
            $result[$fileArray['name']] = $parentProduct;
        }

        var_dump($result);
        die();
    }

    /**
     * @param \Intervention\Image\Image $img
     * @return \Intervention\Image\Image
     */
    private function resizeImg(\Intervention\Image\Image $img)
    {
        $width    = $img->getWidth();
        $height   = $img->getHeight();
        $maxWidth = self::IMAGE_MAX_WIDTH;

        if ($width >=  $height) {
            if ($img->getWidth() < $maxWidth) {
                return $img;
            }

            $img->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $img;
        }

        $img->resize(null, $maxWidth, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $img;
    }
}
