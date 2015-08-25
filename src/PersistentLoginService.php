<?php
namespace Acelaya\PersistentLogin;

use Acelaya\PersistentLogin\Adapter\AuthenticationInterface;
use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Adapter\TokenInterface;
use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;
use Acelaya\PersistentLogin\Model\PersistentSession;

class PersistentLoginService implements PersistentLoginServiceInterface
{
    /**
     * @var StorageInterface
     */
    protected $storageAdapter;
    /**
     * @var TokenInterface
     */
    protected $tokenAdapter;

    public function __construct(StorageInterface $storageAdapter, TokenInterface $tokenAdapter)
    {
        $this->storageAdapter = $storageAdapter;
        $this->tokenAdapter = $tokenAdapter;
    }

    /**
     * Tries to authenticate by using current data.
     * A Result will be returned telling if it was possible to perform the authentication
     *
     * @param AuthenticationInterface $authAdapter
     * @return Result
     */
    public function authenticate(AuthenticationInterface $authAdapter)
    {
        if (! $this->hasPersistentLogin()) {
            return new Result();
        }

        // Try to find a session identified by current token
        $token = $this->tokenAdapter->getToken();
        $session = $this->storageAdapter->findSessionByToken($token);
        if (! isset($session)) {
            return new Result();
        }

        // If this session has been invalidated, has expired or does not belong to a valid identity, don't continue
        $identity = $session->getIdentity();
        if ($session->hasExpired() || ! $session->isValid() || ! isset($identity)) {
            return new Result();
        }

        // Try to authenticate this identity
        $authenticated = $authAdapter->authenticate($identity);

        // Regenarte persistent login, making it last until the old expiration date
        if ($authenticated) {
            $this->invalidateCurrentLogin();
            $this->createNewLogin($identity, $session->getExpirationDate()->getTimestamp() - time());
        }

        return $authenticated;
    }

    /**
     * Invalidates the persistent login both in the storage and session adapters
     */
    public function invalidateCurrentLogin()
    {
        if (! $this->hasPersistentLogin()) {
            return;
        }

        // Invalidate the stored session
        $token = $this->tokenAdapter->getToken();
        $this->storageAdapter->invalidateSessionByToken($token);

        // Invalidate the token too
        $this->tokenAdapter->invalidateToken();
    }

    /**
     * Discards current persistent login if any and creates and persists a new one
     *
     * @param IdentityProviderInterface $identity
     * @param $lifetime
     */
    public function createNewLogin(IdentityProviderInterface $identity, $lifetime = self::DEFAULT_LIFETIME)
    {
        $expirationDate = new \DateTime();
        $expirationDate->setTimestamp(time() + $lifetime);

        // Create the hash of a unique ID prefixed by current date and identity
        $token = hash(
            'sha512',
            sprintf('%s_%s_%s', date('YmdHis'), $identity->getIdentity(), uniqid())
        );

        // Persist this session in current storage
        $this->storageAdapter->persistSession(
            (new PersistentSession())->setToken($token)
                                     ->setExpirationDate($expirationDate)
                                     ->setIdentity($identity)
                                     ->setValid()
        );
        // Persist the token too
        $this->tokenAdapter->setToken($token, $expirationDate);
    }

    /**
     * Checks if it is possible to try an authentication
     *
     * @return bool
     */
    public function hasPersistentLogin()
    {
        return $this->tokenAdapter->hasToken();
    }
}
