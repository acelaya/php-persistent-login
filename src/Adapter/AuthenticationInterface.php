<?php
namespace Acelaya\PersistentLogin\Adapter;

use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

interface AuthenticationInterface
{
    /**
     * @param IdentityProviderInterface $identity
     * @return bool
     */
    public function authenticate(IdentityProviderInterface $identity);
}
