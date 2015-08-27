<?php
namespace Acelaya\PersistentLogin\Model;

use Acelaya\PersistentLogin\Identity\IdentityProviderInterface;
use Acelaya\PersistentLogin\Util\ArraySerializableInterface;

class PersistentSession implements ArraySerializableInterface
{
    /**
     * @var string
     */
    protected $token;
    /**
     * @var \DateTime
     */
    protected $expirationDate;
    /**
     * @var IdentityProviderInterface
     */
    protected $identity;
    /**
     * @var bool
     */
    protected $valid;

    public function __construct()
    {
        $this->valid = false;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    /**
     * Tells if this session has expired
     *
     * @return bool
     */
    public function hasExpired()
    {
        return ! isset($this->expirationDate) || $this->expirationDate < new \DateTime();
    }

    /**
     * @return IdentityProviderInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param IdentityProviderInterface $identity
     * @return $this
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     * @return $this
     */
    public function setValid($valid = true)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->setToken(isset($data['token']) ? $data['token'] : null)
             ->setValid(isset($data['valid']) && $data['valid'] === true)
             ->setExpirationDate(
                 new \DateTime(isset($data['expiration_date']) ? $data['expiration_date'] : 'now')
             );

        // The identity should be provided by a callable
        if (isset($data['identity']) && is_callable($data['identity'])) {
            $this->setIdentity(call_user_func($data['identity']));
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'token' => $this->token,
            'expiration_date' => $this->expirationDate->format('Y-m-d H:i:s'),
            'valid' => $this->valid,
            'identity' => $this->identity
        ];
    }
}
