<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSession;
use Acelaya\PersistentLogin\Model\PersistentSessionInterface;

abstract class AbstractStorage implements StorageInterface
{
    const DEFAULT_SESSION_CLASS_NAME = PersistentSession::class;

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
        if (! is_subclass_of($sessionClassName, PersistentSessionInterface::class)) {
            throw new RuntimeException(sprintf(
                'Invalid session class name provided. It must implement "%s"',
                PersistentSessionInterface::class
            ));
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
