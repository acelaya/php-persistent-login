<?php
namespace Acelaya\PersistentLogin\Adapter;

use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSessionInterface;

interface StorageInterface
{
    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSessionInterface|null
     */
    public function findSessionByToken($token);

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     * @throws RuntimeException
     */
    public function invalidateSessionByToken($token);

    /**
     * Persists provided session
     *
     * @param PersistentSessionInterface $session
     * @throws RuntimeException
     */
    public function persistSession(PersistentSessionInterface $session);
}
