<?php

namespace PredicWCPhoto\Traits;

/**
 * Class FilesHelper
 *
 * @package PredicWCPhoto\Helpers
 */
trait FilesTrait
{
    /**
     * Create directory if doesn't exists
     *
     * @param string $path Directory path
     * @throws \Exception
     * @return bool
     */
    public function mkDir($path)
    {
        if (file_exists($path)) {
            return true;
        }

        if (! mkdir($path, 0755, true)) {
            throw new \Exception(
                sprintf(
                    esc_html__('Error. Could not create dir: %s.', 'predic-wc-photography'),
                    $path
                ),
                500
            );
        }

        return true;
    }
}
