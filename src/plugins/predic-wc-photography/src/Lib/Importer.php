<?php

namespace PredicWCPhoto\Lib;

use PredicWCPhoto\Contracts\ImporterInterface;

class Importer implements ImporterInterface
{
    // TODO: Do all of this next
    /**
     * Check file type to jpeg
     * Leave original file to attach to downloadable product
     * Resize image for frontend usage packagist lib
     * Add watermark
     * Import to variable product from photo and add global product attributes
     * Parse metadata and add tags and categories
     * Make delete product button to delete linked images and zip files. Check if product delete zip files when deleted
     */


	/**
	 * @inheritDoc
	 */
	public function import($photos)
	{
		// TODO: Implement import() method.
	}
}
