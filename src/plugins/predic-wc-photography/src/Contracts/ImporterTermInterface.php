<?php

namespace PredicWCPhoto\Contracts;

/**
 * Interface ImporterTermInterface
 *
 * @package PredicWCPhoto\Contracts
 */
interface ImporterTermInterface
{
    /**
     * Import single term if does not exists
     * @param string $name
     * @param string $taxonomy
     * @throws \Exception
     * @return false|int
     */
    public function import($name, $taxonomy);
}
