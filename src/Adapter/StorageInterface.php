<?php
namespace Acelaya\PersistentLogin\Adapter;

use Acelaya\PersistentLogin\Model\PersistentSession;

interface StorageInterface
{
    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSession|null
     */
    public function findSessionByToken($token);

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     */
    public function invalidateSessionByToken($token);

    /**
     * Persists provided session
     *
     * @param PersistentSession $session
     */
    public function persistSession(PersistentSession $session);
}
