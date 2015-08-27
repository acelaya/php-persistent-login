<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSessionInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Doctrine implements StorageInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;
    /**
     * @var string
     */
    protected $sessionEntityClass;

    /**
     * @param ObjectManager $om
     * @param string $sessionEntityClass The name of your entity implementing PersistentSessionInterface
     */
    public function __construct(ObjectManager $om, $sessionEntityClass)
    {
        // If provided entity class cannot be serialized, throw an exception
        if (! is_subclass_of($sessionEntityClass, 'Acelaya\PersistentLogin\Model\PersistentSessionInterface')) {
            throw new RuntimeException(
                'Invalid session class name provided. '
                . 'It must implement "Acelaya\PersistentLogin\Model\PersistentSessionInterface"'
            );
        }

        $this->om = $om;
        $this->sessionEntityClass = $sessionEntityClass;
    }

    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSessionInterface|null
     */
    public function findSessionByToken($token)
    {
        return $this->om->getRepository($this->sessionEntityClass)->findOneBy([
            'token' => $token
        ]);
    }

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     * @throws RuntimeException
     */
    public function invalidateSessionByToken($token)
    {
        try {
            $entity = $this->om->getRepository($this->sessionEntityClass)->findOneBy([
                'token' => $token
            ]);
            if (! isset($entity)) {
                return;
            }

            $this->om->remove($entity);
            $this->om->flush();
        } catch (\Exception $e) {
            throw new RuntimeException('Something went wrong while invalidating a session', -1, $e);
        }
    }

    /**
     * Persists provided session
     *
     * @param PersistentSessionInterface $session
     * @throws RuntimeException
     */
    public function persistSession(PersistentSessionInterface $session)
    {
        try {
            $class = $this->sessionEntityClass;
            /** @var PersistentSessionInterface $entity */
            $entity = new $class();
            $entity->setIdentity($session->getIdentity())
                   ->setExpirationDate($session->getExpirationDate())
                   ->setToken($session->getToken())
                   ->setValid($session->isValid());

            $this->om->persist($entity);
            $this->om->flush();
        } catch (\Exception $e) {
            throw new RuntimeException('Something went wrong while persisting a session', -1, $e);
        }
    }
}
