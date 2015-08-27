<?php
namespace Acelaya\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\TokenInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Psr7Http implements TokenInterface
{
    const COOKIE_PATTERN = '%s=%s; path=/; expires=%s';

    /**
     * @var ServerRequestInterface;
     */
    protected $request;
    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
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
        return array_key_exists(self::DEFAULT_TOKEN_NAME, $this->request->getCookieParams());
    }

    /**
     * Returns a token if it has been set or null otherwise
     *
     * @return string|null
     */
    public function getToken()
    {
        return ! $this->hasToken() ? null : $this->request->getCookieParams()[self::DEFAULT_TOKEN_NAME];
    }

    /**
     * Sets the token that identifies this session to be valid until expiration time
     *
     * @param $value
     * @param \DateTime $expirationDate
     * @return ResponseInterface
     */
    public function setToken($value, \DateTime $expirationDate)
    {
        return $this->response->withHeader('Set-Cookie', sprintf(
            self::COOKIE_PATTERN,
            self::DEFAULT_TOKEN_NAME,
            $value,
            $expirationDate->getTimestamp()
        ));
    }

    /**
     * Invalidates current token if exists
     * @return ResponseInterface
     */
    public function invalidateToken()
    {
        return $this->response->withHeader('Set-Cookie', sprintf(
            self::COOKIE_PATTERN,
            self::DEFAULT_TOKEN_NAME,
            '',
            time() - 86400
        ));
    }
}
