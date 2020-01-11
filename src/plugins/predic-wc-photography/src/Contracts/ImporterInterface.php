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
     * @return int
	 * @throws \Exception
     */
    public function import($photos);
}
