<?php
namespace AcelayaTest\PersistentLogin\Mock;

use Acelaya\PersistentLogin\Adapter\TokenInterface;

class TokenAdapterMock implements TokenInterface
{
    protected $token = [];

    /**
     * Tells if this session adapter has a session token
     *
     * @return bool
     */
    public function hasToken()
    {
        return
            isset($this->token['value'])
            && isset($this->token['expirationDate'])
            && $this->token['expirationDate'] >= new \DateTime();
    }

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken()
    {
        if (! $this->hasToken()) {
            return null;
        }

        return $this->token['value'];
    }

    /**
     * Sets the token that identifies this session to be valid until expiration time
     *
     * @param $value
     * @param \DateTime $expirationDate
     */
    public function setToken($value, \DateTime $expirationDate)
    {
        $this->token = [
            'value' => $value,
            'expirationDate' => $expirationDate
        ];
    }

    /**
     * Invalidates current token if exists
     */
    public function invalidateToken()
    {
        $this->token = [];
    }
}
