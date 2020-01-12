<?php

namespace PredicWCPhoto\Lib;

use Intervention\Image\ImageManagerStatic as Image;
use PredicWCPhoto\Contracts\ImporterImagesInterface;
use PredicWCPhoto\Traits\FilesTrait;
use Spatie\ImageOptimizer\OptimizerChainFactory as ImageOptimizer;

/**
 * Class ImporterImages
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterImages implements ImporterImagesInterface
{
    use FilesTrait;

    /**
     * Width to resize image if larger
     */
    private const IMAGE_MAX_WIDTH = 1920;

    /**
     * Image quality for previews
     */
    private const IMAGE_QUALITY   = 80;

    /**
     * @var string
     */
    private $watermarkImagePath;

    /**
     * Temporary path to save images after manipulation before importing them
     * @var string
     */
    private $tmpUploadDirPath ;

    /**
     * ImporterImages constructor.
     */
    public function __construct()
    {
        $config                   = predic_wc_photography_helpers()->config;
        $this->watermarkImagePath = $config->getPluginImagesPath() . '/importer/watermark.png';

        $uploadDir              = wp_upload_dir();
        $this->tmpUploadDirPath = $uploadDir['basedir'] . '/' . 'pwpc-tmp';
    }

    /**
     * Import image
     *
     * @param string $filename Filename
     * @param string $imgPath Image path
     * @param string $caption Image caption
     * @param bool|\WC_Product $product
     * @throws \Exception
     * @return int Image id in WP db
     */
    public function import($filename, $imgPath, $caption = '', $product = false)
    {
        $this->mkDir($this->tmpUploadDirPath);
        $tmpFilePath = $this->tmpUploadDirPath . '/' . $filename;

        $productImageId    = is_a($product, '\WC_Product') ? $product->get_image_id() : false;

        // Delete image attached and re upload new so we can update new metadata and image
        if (! empty($productImageId)) {
            $bool = false !== wp_delete_post($productImageId, true);

            if (! $bool) {
                throw new \Exception(
                    sprintf(
                        esc_html__('Error deleting product image with id %s.', 'predic-wc-photography'),
                        $productImageId
                    ),
                    500
                );
            }
        }

        // Delete previous created file if doing this second time and file exists
        if (file_exists($tmpFilePath)) {
            unlink($tmpFilePath);
        }

        /**
         * https://packagist.org/packages/intervention/image
         */
        $img = Image::make($imgPath);

        /**
         * Resize image
         */
        $img = $this->resizeImg($img);

        /**
         * Insert a watermark - Other position: bottom-left
         */
        $img->insert(Image::make($this->watermarkImagePath), 'center');

        // save image in desired format
        $img->save($tmpFilePath, self::IMAGE_QUALITY);

        /**
         * Optimize image
         *
         * @important: Make sure server has installed or nothing will happen
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

        /**
         * Import image
         */
        $imageId = media_handle_sideload(
            $fileArray,
            null,
            null,
            [
                'post_content' => $caption
            ]
        );

        // Delete tmp file, if media_handle_sideload didn't deleted it
        if (file_exists($tmpFilePath)) {
            unlink($tmpFilePath);
        }

        if (is_wp_error($imageId)) {
            throw new \Exception(
                sprintf(
                    esc_html__('Error importing image %s. Error: %s', 'predic-wc-photography'),
                    $filename,
                    $imageId->get_error_message()
                ),
                is_int($imageId->get_error_code()) ? $imageId->get_error_code() : 500
            );
        }

        return $imageId;
    }

    /**
     * Resize image
     *
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
