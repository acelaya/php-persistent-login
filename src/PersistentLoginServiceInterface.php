<?php
namespace Acelaya\PersistentLogin;

use Acelaya\PersistentLogin\Adapter\AuthenticationInterface;
use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;

interface PersistentLoginServiceInterface
{
    const DEFAULT_LIFETIME = 1209600; // Two weeks

    /**
     * Tries to authenticate by using current data.
     * A Result will be returned telling if it was possible to perform the authentication
     *
     * @param AuthenticationInterface $authAdapter
     * @return Result
     */
    public function authenticate(AuthenticationInterface $authAdapter);

    /**
     * Invalidates the persistent login both in the storage and session adapters
     */
    public function invalidateCurrentLogin();

    /**
     * Discards current persistent login if any and creates and persists a new one
     *
     * @param IdentityProviderInterface $identity
     * @param $lifetime
     */
    public function createNewLogin(IdentityProviderInterface $identity, $lifetime = self::DEFAULT_LIFETIME);

    /**
     * Checks if it is possible to try an authentication
     *
     * @return bool
     */
    public function hasPersistentLogin();
}
