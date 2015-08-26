<?php
namespace AcelayaTest\PersistentLogin\Mock;

use Acelaya\PersistentLogin\Adapter\AuthenticationInterface;
use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

class AuthenticationAdapterMock implements AuthenticationInterface
{
    /**
     * @var IdentityProviderInterface[]
     */
    protected $identities;

    /**
     * @param IdentityProviderInterface[] $identities
     */
    public function __construct(array $identities = [])
    {
        $this->identities = $identities;
    }

    /**
     * @param IdentityProviderInterface $identity
     * @return bool
     */
    public function authenticate(IdentityProviderInterface $identity)
    {
        return in_array($identity, $this->identities);
    }
}
