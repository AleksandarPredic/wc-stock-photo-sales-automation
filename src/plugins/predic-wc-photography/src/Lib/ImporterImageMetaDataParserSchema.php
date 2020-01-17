<?php

namespace PredicWCPhoto\Lib;

/**
 * Class ImporterImageMetaDataParserSchema
 *
 * @package PredicWCPhoto\Lib
 */
class ImporterImageMetaDataParserSchema
{

    /**
     * Camera model name
     * @var string
     */
    protected $camera;

    /**
     * Image resolution in px
     * @var string
     */
    protected $resolution;

    /**
     * Image type
     * @var string
     */
    protected $type;

    /**
     * Image document name from editing program if set
     * @var string
     */
    protected $name;

    /**
     * Image description
     * @var string
     */
    protected $description;

    /**
     * Image keywords in a single dimension non associative array
     * @var array
     */
    protected $keyWords;

	/**
	 * Names of the shootout to be added as custom taxonomy
	 * @var array
	 */
	protected $shootout;

	/**
	 * Name of the models to be added as custom taxonomy
	 * @var array
	 */
	protected $models;

    /**
     * Timestamp of a photo taken - recorded in camera device
     * @var int
     */
    protected $cameraUploadDate;
}
