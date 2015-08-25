<?php
namespace Acelaya\PersistentLogin\Identity;

interface IdentityProviderInterface
{
    /**
     * Returns the identity
     *
     * @return string
     */
    public function getIdentity();

    /**
     * Returns the credential
     *
     * @return string
     */
    public function getCredential();
}
