<?php
namespace Acelaya\PersistentLogin\Model;

use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

interface PersistentSessionInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token);

    /**
     * @return \DateTime
     */
    public function getExpirationDate();

    /**
     * @param \DateTime $expirationDate
     * @return $this
     */
    public function setExpirationDate(\DateTime $expirationDate);

    /**
     * Tells if this session has expired
     *
     * @return bool
     */
    public function hasExpired();

    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @param boolean $valid
     * @return $this
     */
    public function setValid($valid = true);

    /**
     * @return IdentityProviderInterface
     */
    public function getIdentity();

    /**
     * @param IdentityProviderInterface $identity
     * @return $this
     */
    public function setIdentity(IdentityProviderInterface $identity);
}
