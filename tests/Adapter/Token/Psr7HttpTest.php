<?php
namespace AcelayaTest\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\Token\Psr7Http;
use Acelaya\PersistentLogin\Adapter\TokenInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class Psr7HttpTest extends TestCase
{
    /**
     * @var Psr7Http
     */
    protected $adapter;
    /**
     * @var ServerRequest
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    public function setUp()
    {
        $this->init();
    }

    public function testHasToken()
    {
        $this->assertFalse($this->adapter->hasToken());

        $this->init([
            'request' => (new ServerRequest())->withCookieParams([
                TokenInterface::DEFAULT_TOKEN_NAME => 'foo'
            ])
        ]);
        $this->assertTrue($this->adapter->hasToken());
    }

    public function testGetToken()
    {
        $this->assertNull($this->adapter->getToken());

        $expected = 'foo';
        $this->init([
            'request' => (new ServerRequest())->withCookieParams([
                TokenInterface::DEFAULT_TOKEN_NAME => $expected
            ])
        ]);
        $this->assertEquals($expected, $this->adapter->getToken());
    }

    public function testSetToken()
    {
        $this->assertEmpty($this->response->getHeaders());

        $expected = 'foo';
        $now = new \DateTime();
        $response = $this->adapter->setToken($expected, $now);
        $this->assertEmpty($this->response->getHeaders());
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
        $this->assertArrayHasKey('Set-Cookie', $response->getHeaders());
        $this->assertContains(
            sprintf(Psr7Http::COOKIE_PATTERN, TokenInterface::DEFAULT_TOKEN_NAME, $expected, $now->getTimestamp()),
            $response->getHeader('Set-cookie')
        );
    }

    public function testInvalidateToken()
    {
        $this->assertEmpty($this->response->getHeaders());

        $response = $this->adapter->invalidateToken();
        $this->assertEmpty($this->response->getHeaders());
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
        $this->assertArrayHasKey('Set-Cookie', $response->getHeaders());
    }

    protected function init(array $messages = [])
    {
        $this->request = isset($messages['request']) ? $messages['request'] : new ServerRequest();
        $this->response = isset($messages['response']) ? $messages['response'] : new Response();
        $this->adapter = new Psr7Http($this->request, $this->response);
    }
}
