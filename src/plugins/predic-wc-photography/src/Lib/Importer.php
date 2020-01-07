<?php

namespace PredicWCPhoto\Lib;

use Intervention\Image\ImageManagerStatic as Image;
use Spatie\ImageOptimizer\OptimizerChainFactory as ImageOptimizer;
use PredicWCPhoto\Contracts\ImporterInterface;

class Importer implements ImporterInterface
{
    // TODO: Do all of this next

    /**
     * @var string
     */
    private $watermarkImagePath;

    const IMAGE_MAX_WIDTH = 1920;
    const IMAGE_QUALITY = 75;

    /**
     * Leave original file to attach to downloadable product
     * Resize image for frontend usage packagist lib
     * Add watermark
     * Import to variable product from photo and add global product attributes
     * Parse metadata and add tags and categories
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted
     */

    public function __construct()
    {
        $this->watermarkImagePath = predic_wc_photography_helpers()->config->getPluginImagesPath() . '/importer/watermark.png';
    }

    /**
     * @inheritDoc
     */
    public function import($photos)
    {
        $result = [];

        // TODO: Create folder for manipulation so we can manually delete leftover images
        $uploadDir     = wp_upload_dir();
        $uploadDirPath = $uploadDir['basedir'];

        foreach ($photos as $photo) {
            var_dump($photo);

            $filename = $photo['filename'];

            $tmpFilePath         = $uploadDirPath . '/' . $filename;
            $originalUploadedImg = $photo['tmp_name']; // or use backup for file

            // Delete previous created file if doing this second time and file exists
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }

            // open an image file
			/**
			 * https://packagist.org/packages/intervention/image
			 */
            $img = Image::make($photo['tmp_name']);

            // Resize
			$img = $this->resizeImg($img);

            // insert a watermark
            $img->insert(Image::make($this->watermarkImagePath)->resize($img->getWidth(), null), 'center');

            // save image in desired format
            $img->save($tmpFilePath, 75);

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
        $width  = $img->getWidth();
        $height = $img->getHeight();
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
