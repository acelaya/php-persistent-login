<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Exception\RuntimeException;
use Acelaya\PersistentLogin\Model\PersistentSession;
use Acelaya\PersistentLogin\Util\ArraySerializableInterface;
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
     * @param string $sessionEntityClass The name of your entity implementing ArraySerializableInterface
     */
    public function __construct(ObjectManager $om, $sessionEntityClass)
    {
        // If provided entity class cannot be serialized, throw an exception
        if (! is_subclass_of($sessionEntityClass, 'Acelaya\PersistentLogin\Util\ArraySerializableInterface')) {
            throw new RuntimeException(
                'Returned entity cannot be serialized. '
                . 'It should implement "Acelaya\PersistentLogin\Util\ArraySerializableInterface"'
            );
        }

        $this->om = $om;
        $this->sessionEntityClass = $sessionEntityClass;
    }

    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSession|null
     */
    public function findSessionByToken($token)
    {
        $entity = $this->om->getRepository($this->sessionEntityClass)->findOneBy([
            'token' => $token
        ]);
        if ($entity === null) {
            return null;
        }

        $session = new PersistentSession();
        $session->exchangeArray($entity->getArrayCopy());

        return $session;
    }

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     * @throws RuntimeException
     */
    public function invalidateSessionByToken($token)
    {
        $entity = $this->om->getRepository($this->sessionEntityClass)->findOneBy([
            'token' => $token
        ]);
        if (! isset($entity)) {
            return;
        }

        $this->om->remove($entity);
        $this->om->flush();
    }

    /**
     * Persists provided session
     *
     * @param PersistentSession $session
     * @throws RuntimeException
     */
    public function persistSession(PersistentSession $session)
    {
        try {

        } catch (\Exception $e) {

        }
        $class = $this->sessionEntityClass;
        /** @var ArraySerializableInterface $entity */
        $entity = new $class();
        $entity->exchangeArray($session->getArrayCopy());
        $this->om->persist($entity);
        $this->om->flush();
    }
}
