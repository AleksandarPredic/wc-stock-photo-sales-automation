<?php

namespace PredicWCPhoto\Contracts;

interface ImporterInterface
{

    /**
	 * @param array $photos
     * @return bool
     */
    public function import($photos);
}
