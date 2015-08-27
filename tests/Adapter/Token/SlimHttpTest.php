<?php
namespace AcelayaTest\PersistentLogin\Adapter\Token;

use Acelaya\PersistentLogin\Adapter\Token\SlimHttp;
use Acelaya\PersistentLogin\Adapter\TokenInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Slim\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class SlimHttpTest extends TestCase
{
    /**
     * @var SlimHttp
     */
    private $adapter;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

    public function setUp()
    {
        $this->request = new Request(Environment::mock());
        $this->response = new Response();
        $this->adapter = new SlimHttp($this->request, $this->response);
    }

    public function testHasToken()
    {
        $this->assertFalse($this->adapter->hasToken());
        $this->request->cookies->set(TokenInterface::DEFAULT_TOKEN_NAME, 'foo');
        $this->assertTrue($this->adapter->hasToken());
    }

    public function testGetToken()
    {
        $this->assertNull($this->adapter->getToken());
        $expected = 'foo';
        $this->request->cookies->set(TokenInterface::DEFAULT_TOKEN_NAME, $expected);
        $this->assertEquals($expected, $this->adapter->getToken());
    }

    public function testSetToken()
    {
        $this->assertEmpty($this->response->cookies);

        $expected = 'foo';
        $now = new \DateTime();
        $this->adapter->setToken($expected, $now);
        $this->assertCount(1, $this->response->cookies);
        $cookiesArray = $this->response->cookies->getIterator()->getArrayCopy();
        $this->assertArrayHasKey(TokenInterface::DEFAULT_TOKEN_NAME, $cookiesArray);
        $this->assertEquals([
            'value' => $expected,
            'domain' => null,
            'path' => '/',
            'expires' => $now->getTimestamp(),
            'secure' => false,
            'httponly' => false
        ], $cookiesArray[TokenInterface::DEFAULT_TOKEN_NAME]);
    }

    public function testInvalidateToken()
    {
        $this->assertEmpty($this->response->cookies);
        $this->adapter->invalidateToken();
        $this->assertCount(1, $this->response->cookies);
        $this->assertArrayHasKey(TokenInterface::DEFAULT_TOKEN_NAME, $this->response->cookies);
    }
}
