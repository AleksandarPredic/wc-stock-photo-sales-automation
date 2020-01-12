<?php

namespace PredicWCPhoto\Contracts;

/**
 * Interface ImporterInterface
 *
 * @package PredicWCPhoto\Contracts
 */
interface ImporterInterface
{

    /**
     * Return parent product id
     * @param array $photos
     * @return array
     */
    public function import($photos);
}
