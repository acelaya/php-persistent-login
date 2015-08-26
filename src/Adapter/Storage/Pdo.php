<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Model\PersistentSession;

class Pdo implements StorageInterface
{
    /**
     * @var \PDO
     */
    protected $pdo;
    /**
     * @var string
     */
    protected $tableName;

    public function __construct(\PDO $pdo, $tableName = 'persistent_session')
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
    }

    /**
     * Tries to find a persistent session based on provided token
     *
     * @param string $token
     * @return PersistentSession|null
     */
    public function findSessionByToken($token)
    {
        $statement = $this->pdo->prepare('SELECT * FROM :table WHERE `token` = :token');
        $statement->bindValue('table', $this->tableName);
        $statement->bindValue('token', $token);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        $session = new PersistentSession();
        $session->setToken($token)
                ->setValid(isset($result['valid']) && $result['valid'] === true)
                ->setExpirationDate(
                    new \DateTime(isset($result['expiration_date']) ? $result['expiration_date'] : 'now')
                )
                ->setIdentity(null); // TODO Set the identity somehow

        return $session;
    }

    /**
     * Sets the session of provided token as invalid
     *
     * @param $token
     */
    public function invalidateSessionByToken($token)
    {
        $statement = $this->pdo->prepare('DELETE FROM :table WHERE `token` = :token LIMIT 1');
        $statement->bindValue('table', $this->tableName);
        $statement->bindValue('token', $token);
        $statement->execute();
    }

    /**
     * Persists provided session
     *
     * @param PersistentSession $session
     */
    public function persistSession(PersistentSession $session)
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO :table '
            . 'SET `token` = :token, `expiration_date` = :expirationDate, `identity_id`=:identityId, `valid` = :valid'
        );
        $statement->bindValue('table', $this->tableName);
        $statement->bindValue('token', $session->getToken());
        $statement->bindValue('expirationDate', $session->getExpirationDate()->format('Y-m-d H:i:s'));
        $statement->bindValue(
            'identityId',
            method_exists($session->getIdentity(), 'getId') ? $session->getIdentity()->getid() : null
        );
        $statement->bindValue('valid', $session->isValid() ? '1' : '0');
        $statement->execute();
    }
}
