<?php
namespace Acelaya\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\TokenInterface;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Http\Response;

class ZF2Http implements TokenInterface
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
        $cookies = $this->request->getCookie();
        if (! $cookies) {
            return false;
        }

        return $cookies->offsetExists(self::DEFAULT_TOKEN_NAME);
    }

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken()
    {
        return ! $this->hasToken() ? null : $this->request->getCookie()->offsetGet(self::DEFAULT_TOKEN_NAME);
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
        // Add a Set-Cookie header in the response
        $cookie = new SetCookie(self::DEFAULT_TOKEN_NAME, $value, $expirationDate->getTimestamp(), '/');
        $this->response->getHeaders()->addHeader($cookie);
    }

    /**
     * Invalidates current token if exists
     * @return object|null
     */
    public function invalidateToken()
    {
        $cookie = new SetCookie(self::DEFAULT_TOKEN_NAME, '', time() - 86400, '/');
        $this->response->getHeaders()->addHeader($cookie);
    }
}
