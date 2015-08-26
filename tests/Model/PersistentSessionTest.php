<?php
namespace AcelayaTest\PersistentLogin\Model;

use Acelaya\PersistentLogin\Model\PersistentSession;
use AcelayaTest\PersistentLogin\Mock\IdentityMock;
use PHPUnit_Framework_TestCase as TestCase;

class PersistentSessionTest extends TestCase
{
    /**
     * @var PersistentSession
     */
    private $session;

    public function setUp()
    {
        $this->session = new PersistentSession();
    }

    public function testToken()
    {
        $expected = 'the_token';
        $this->assertNull($this->session->getToken());
        $this->assertSame($this->session, $this->session->setToken($expected));
        $this->assertEquals($expected, $this->session->getToken());
    }

    public function testExpirationDate()
    {
        $expected = new \DateTime();
        $this->assertNull($this->session->getExpirationDate());
        $this->assertSame($this->session, $this->session->setExpirationDate($expected));
        $this->assertSame($expected, $this->session->getExpirationDate());
    }

    public function testIdentity()
    {
        $expected = new IdentityMock();
        $this->assertNull($this->session->getIdentity());
        $this->assertSame($this->session, $this->session->setIdentity($expected));
        $this->assertSame($expected, $this->session->getIdentity());
    }

    public function testValid()
    {
        $this->assertFalse($this->session->isValid());
        $this->assertSame($this->session, $this->session->setValid());
        $this->assertTrue($this->session->isValid());
    }
}
