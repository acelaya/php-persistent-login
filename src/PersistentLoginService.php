<?php
namespace Acelaya\PersistentLogin;

use Acelaya\PersistentLogin\Adapter\AuthenticationInterface;
use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

class PersistentLoginService implements PersistentLoginServiceInterface
{
    /**
     * Tries to authenticate by using current data.
     * A Result will be returned telling if it was possible to perform the authentication
     *
     * @param AuthenticationInterface $authAdapter
     * @return Result
     */
    public function authenticateWithCurrentLogin(AuthenticationInterface $authAdapter)
    {
        // TODO: Implement authenticateWithCurrentLogin() method.
    }

    /**
     * Invalidates the persistent login both in the storage and session adapters
     *
     * @return mixed
     */
    public function invalidateCurrentLogin()
    {
        // TODO: Implement invalidateCurrentLogin() method.
    }

    /**
     * Discards current persistent login if any and creates and persists a new one
     *
     * @param IdentityProviderInterface $identity
     * @param $lifetime
     * @return mixed
     */
    public function createNewLogin(IdentityProviderInterface $identity, $lifetime)
    {
        // TODO: Implement createNewLogin() method.
    }

    /**
     * Checks if it is possible to try an authentication
     *
     * @return bool
     */
    public function hasPersistentLogin()
    {
        // TODO: Implement hasPersistentLogin() method.
    }
}
