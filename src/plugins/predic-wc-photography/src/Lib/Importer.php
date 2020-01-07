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
	 * @var \WC_Importer_Interface
	 */
	private $wcImporter;

	/**
     * Leave original file to attach to downloadable product
     * Import to variable product from photo and add global product attributes
	 * Add zip for download for both variations
     * Parse metadata and add tags and categories
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted
     */

    public function __construct()
    {
    	$config = predic_wc_photography_helpers()->config;
        $this->watermarkImagePath = $config->getPluginImagesPath() . '/importer/watermark.png';
        $this->pluginSlug = $config->getPluginSlug();
    }

    /**
     * @inheritDoc
     */
    public function import($photos)
    {
        $result = [];

		// Set temporary folder to manipulate images
        $uploadDir     = wp_upload_dir();
        $uploadDirPath = $uploadDir['basedir'] . '/' . sanitize_file_name($this->pluginSlug);

		if (!file_exists($uploadDirPath)) {
			mkdir($uploadDirPath, 0755, true);
		}

        foreach ($photos as $photo) {
			$wcImporter = new WCImporter();

			$filename = $photo['filename'];

            $tmpUploadPath       = $photo['tmp_name'];
            $tmpFilePath         = $uploadDirPath . '/' . $filename;

            // Read metadata
            // TODO: handle metadata reading
            /*$metaData = exif_read_data($tmpUploadPath);
            var_dump($metaData);*/


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

            // insert a watermark
            $img->insert(Image::make($this->watermarkImagePath)->resize($img->getWidth(), null), 'center');

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

            // TODO: Check if exist by filename which will be sku or do update for product
            // https://wordpress.stackexchange.com/questions/70573/checking-if-a-file-is-already-in-the-media-library
            $imgaeId = media_handle_sideload(
                $fileArray
            );

            $result[$fileArray['name']] = $imgaeId;

            // Delete tmp file, if media_handle_sideload didn't deleted it
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }


			/**
			 * Create products first before any image manipulation
			 */
			// Test product creation
			$basename = basename($filename, sprintf('.%s', $img->extension));

			$wcImporter->setData(
				ucfirst(str_replace('-', ' ', $basename)),
				basename($basename),
				'short description',
				'description',
				[
					99, // Regular price
					999 // Extended price
				],
				$imgaeId
			);

			// Unhandled exception
			$parentProduct = $wcImporter->import();
			var_dump($parentProduct);
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
