<?php
namespace Acelaya\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\TokenInterface;

class PhpHttp implements TokenInterface
{
    /**
     * Tells if this session adapter has a session token
     *
     * @return bool
     */
    public function hasToken()
    {
        return isset($_COOKIE[self::DEFAULT_TOKEN_NAME]);
    }

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken()
    {
        return ! $this->hasToken() ? null : $_COOKIE[self::DEFAULT_TOKEN_NAME];
    }

    /**
     * Sets the token that identifies this session to be valid until expiration time
     *
     * @param $value
     * @param \DateTime $expirationDate
     * @return object|null
     */
    public function setToken($value, \DateTime $expirationDate)
    {
        setcookie(self::DEFAULT_TOKEN_NAME, $value, $expirationDate->getTimestamp(), '/');
    }

    /**
     * Invalidates current token if exists
     * @return object|null
     */
    public function invalidateToken()
    {
        setcookie(self::DEFAULT_TOKEN_NAME, '', time() - 86400, '/');
    }
}
