<?php
namespace Acelaya\PersistentLogin\Adapter;

interface TokenInterface
{
    /**
     * Tells if this session adapter has a session token
     *
     * @return bool
     */
    public function hasToken();

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Sets the token that identifies this session to be valid until expiration time
     *
     * @param $value
     * @param \DateTime $expirationDate
     */
    public function setToken($value, \DateTime $expirationDate);

    /**
     * Invalidates current token if exists
     */
    public function invalidateToken();
}
