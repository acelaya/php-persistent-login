<?php
namespace Acelaya\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\TokenInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class SlimHttp implements TokenInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Tells if this session adapter has a session token
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->request->cookies->has(self::DEFAULT_TOKEN_NAME);
    }

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken()
    {
        return ! $this->hasToken() ? null : $this->request->cookies->get(self::DEFAULT_TOKEN_NAME);
    }

    /**
     * Sets the token that identifies this session to be valid until expiration time
     *
     * @param $value
     * @param \DateTime $expirationDate
     * @return object|null
     */
    public function setToken($value, \DateTime $expirationDate)
    {
        $this->response->cookies->set(self::DEFAULT_TOKEN_NAME, [
            'value' => $value,
            'path' => '/',
            'expires' => $expirationDate->getTimestamp()
        ]);
    }

    /**
     * Invalidates current token if exists
     *
     * @return object|null
     */
    public function invalidateToken()
    {
        $this->response->cookies->set(self::DEFAULT_TOKEN_NAME, [
            'value' => '',
            'path' => '/',
            'expires' => time() - 86400
        ]);
    }
}
