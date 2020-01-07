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
	private $downloadableFilesUploadDirPath = 'woocommerce-downloadable-files';

	/**
	 * @var \WC_Importer_Interface
	 */
	private $wcImporter;

	/**
     * Leave original file to attach to downloadable product
     * Import to variable product from photo and add global product attributes
	 * Add zip for download for both variations
     * Parse metadata and add tags and categories
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted or hOOK TO PRODUCT DELETE
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
        $tmpUploadDirPath = $uploadDir['basedir'] . '/' . sanitize_file_name($this->pluginSlug) . '-tmp';
        $downloadableFilesUploadDirPath = $uploadDir['basedir'] . '/' . sanitize_file_name($this->downloadableFilesUploadDirPath);

		if (!file_exists($tmpUploadDirPath)) {
			mkdir($tmpUploadDirPath, 0755, true);
		}

		if (!file_exists($downloadableFilesUploadDirPath)) {
			mkdir($downloadableFilesUploadDirPath, 0755, true);
		}

        foreach ($photos as $photo) {
			$wcImporter = new WCImporter();

			$filename = str_replace('_', '-', strtolower(sanitize_file_name($photo['filename'])));
			$pathInfo = pathinfo($filename);
            $tmpUploadPath       = $photo['tmp_name'];
            $tmpFilePath         = $tmpUploadDirPath . '/' . $filename;
			$productSlug = basename($filename, sprintf('.%s', $pathInfo['extension']));
			$wcProduct = wc_get_product(wc_get_product_id_by_sku($productSlug)); // Check if product exists
			$wcProductImageId = is_a($wcProduct, '\WC_Product') ? $wcProduct->get_image_id() : false;

			// Delete image attached and re upload new so we can update new metadata
			if (! empty($wcProductImageId)) {
				$bool = false !== wp_delete_post( $wcProductImageId, true );

				if ( ! $bool ) {
					throw new \Exception(
						sprintf(
							esc_html__( 'Error deleting product image with id %s.', 'predic-wc-photography' ),
							$this->product->get_id()
						),
						500
					);

				}
			}


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

            // Delete tmp file, if media_handle_sideload didn't deleted it
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }



			/**
			 * Create products first before any image manipulation
			 */
			$wcImporter->setData(
				ucfirst(str_replace('-', ' ', $productSlug)),
				$productSlug,
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
			$parentProductId = $parentProduct['id'];

			if (! isset($parentProduct['children']) || count($parentProduct['children']) < 2) {
				throw new \Exception(
					sprintf(
						esc_html__( 'Error. Parent product with id %s has no children.', 'predic-wc-photography' ),
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
			$downloadableFilePath = $downloadableFilePathParentProductFolder . '/' . $wcProductDownload->get_name() . '.' . $img->extension;

			if (!file_exists($downloadableFilePathParentProductFolder)) {
				mkdir($downloadableFilePathParentProductFolder, 0755, true);
			}

			$moved = copy($tmpUploadPath, $downloadableFilePath);

			if (! $moved) {
				throw new \Exception(
					sprintf(
						esc_html__( 'Error. Downloadable file not copied to destination %s.', 'predic-wc-photography' ),
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
