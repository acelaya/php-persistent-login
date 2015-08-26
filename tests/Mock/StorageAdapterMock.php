<?php
namespace AcelayaTest\PersistentLogin\Mock;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Model\PersistentSession;

class StorageAdapterMock implements StorageInterface
{
    /**
     * @var PersistentSession[]
     */
    protected $sessions;

    /**
     * @param PersistentSession[] $sessions
     */
    public function __construct(array &$sessions = [])
    {
        $this->sessions =& $sessions;
    }

    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSession|null
     */
    public function findSessionByToken($token)
    {
        return isset($this->sessions[$token]) ? $this->sessions[$token] : null;
    }

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     */
    public function invalidateSessionByToken($token)
    {
        unset($this->sessions[$token]);
    }

    /**
     * Persists provided session
     *
     * @param PersistentSession $session
     */
    public function persistSession(PersistentSession $session)
    {
        $this->sessions[$session->getToken()] = $session;
    }
}
