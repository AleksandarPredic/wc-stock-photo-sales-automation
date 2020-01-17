<?php

namespace PredicWCPhoto\Lib;

/**
 * Interface WCTaxonomiesInterface
 *
 * @package PredicWCPhoto\Lib
 */
interface WCTaxonomiesInterface
{
    /**
     * Taxonomy id
     * @var string
     */
    public const SHOOTOUTS_ID = 'photo_shootouts';

    /**
     * Taxonomy id
     * @var string
     */
    public const MODELS_ID = 'photo_models';

    /**
     * Add hooks
     */
    public function init();
}
