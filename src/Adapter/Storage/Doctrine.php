<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSessionInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Doctrine extends AbstractStorage
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
    public function __construct(ObjectManager $om, $sessionEntityClass = null)
    {
        parent::__construct($sessionEntityClass);
        $this->om = $om;
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
            $entity = $this->createSessionObject();
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
