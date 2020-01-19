<?php

namespace PredicWCPhoto\Contracts;

/**
 * Interface ImporterImageMetaDataParserInterface
 *
 * @package PredicWCPhoto\Contracts
 */
interface ImporterImageMetaDataParserInterface
{
    /**
     * Parse metadata from an image
     *
     * @param string $imagePath Path to the image
     */
    public function parse($imagePath);

    /**
     * Return camera model name
     * @return string
     */
    public function getCamera();

    /**
     * Return image resolution in px
     * @return string
     */
    public function getResolution();

    /**
     * Return image type
     * @return string
     */
    public function getType();

    /**
     * Return image document name from editing program if set
     * @return string
     */
    public function getName();

    /**
     * Return image decription
     * @return string
     */
    public function getDescription();

    /**
     * Return image keywords in a single dimension non associative array
     * @return array
     */
    public function getKeywords();

    /**
     * Return Timestamp of a photo taken
     * @return int
     */
    public function getCameraUploadDate();

    /**
     * Return names of the shootout as array
     * @return array
     */
    public function getShootout();

    /**
     * Return models names (people) as array
     * @return array
     */
    public function getModels();

    /**
     * Return prices as array [regular, extended]
     *
     * @@return  array
     */
    public function getPrices();
}
