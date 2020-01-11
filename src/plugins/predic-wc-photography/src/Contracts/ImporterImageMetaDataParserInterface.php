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
     */
    public function parse();

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
}
