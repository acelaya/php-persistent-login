<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSessionInterface;

abstract class AbstractStorage implements StorageInterface
{
    const DEFAULT_SESSION_CLASS_NAME = 'Acelaya\PersistentLogin\Model\PersistentSession';

    /**
     * @var string
     */
    protected $sessionClass;

    public function __construct($sessionClassName = null)
    {
        if (! isset($sessionClassName)) {
            $sessionClassName = self::DEFAULT_SESSION_CLASS_NAME;
        }

        // If provided entity class cannot be serialized, throw an exception
        if (! is_subclass_of($sessionClassName, 'Acelaya\PersistentLogin\Model\PersistentSessionInterface')) {
            throw new RuntimeException(
                'Invalid session class name provided. '
                . 'It must implement "Acelaya\PersistentLogin\Model\PersistentSessionInterface"'
            );
        }

        $this->sessionClass = $sessionClassName;
    }

    /**
     * Creates a new PersistentSession instance of provided type
     *
     * @return PersistentSessionInterface
     */
    protected function createSessionObject()
    {
        $class = $this->sessionClass;
        return new $class();
    }
}
