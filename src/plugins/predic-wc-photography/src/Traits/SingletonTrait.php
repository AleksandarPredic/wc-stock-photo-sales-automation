<?php

namespace PredicWCPhoto\Traits;

/**
 * Trait SingletonTrait
 *
 * @package PredicWCPhoto\Traits
 */
trait SingletonTrait
{
    /**
     * Class instance
     * @var SingletonTrait
     */
    private static $instance;
    /**
     * Return Class instance
     * @return $this
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
