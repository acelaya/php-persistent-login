<?php
namespace Acelaya\PersistentLogin;

use Acelaya\PersistentLogin\Model\PersistentSession;

/**
 * Immutable class representing the result of an authentication
 * @author
 * @link
 */
final class Result
{
    /**
     * @var PersistentSession
     */
    private $session;
    /**
     * @var bool
     */
    private $valid;

    /**
     * @param PersistentSession|null $session
     * @param bool|false $valid
     */
    public function __construct(PersistentSession $session = null, $valid = false)
    {
        $this->session = $session;
        $this->valid = $valid;
    }

    /**
     * @return PersistentSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }
}
