<?php
namespace Acelaya\PersistentLogin\Adapter\Storage;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Exception\RuntimeException;
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
        // TODO Set the identity provider callable somehow
        $session->exchangeArray($result);

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
        try {
            $statement = $this->pdo->prepare('DELETE FROM :table WHERE `token` = :token LIMIT 1');
            $statement->bindValue('table', $this->tableName);
            $statement->bindValue('token', $token);
            $statement->execute();
        } catch (\Exception $e) {
            throw new RuntimeException('Something went wrong while invalidating a session', -1, $e);
        }
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
            $statement = $this->pdo->prepare(
                'INSERT INTO :table SET '
                . '`token` = :token, `expiration_date` = :expirationDate, `identity_id`=:identityId, `valid` = :valid'
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
        } catch (\Exception $e) {
            throw new RuntimeException('Something went wrong while persisting a session', -1, $e);
        }
    }
}
