<?php
namespace AcelayaTest\PersistentLogin\Mock;

use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

class IdentityMock implements IdentityProviderInterface
{
    /**
     * Returns the identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return 'the_identity';
    }

    /**
     * Returns the credential
     *
     * @return string
     */
    public function getCredential()
    {
        return 'the_credential';
    }
}
