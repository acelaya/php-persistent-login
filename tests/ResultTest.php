<?php
namespace AcelayaTest\PersistentLogin;

use Acelaya\PersistentLogin\Model\PersistentSession;
use Acelaya\PersistentLogin\Result;
use PHPUnit_Framework_TestCase as TestCase;

class ResultTest extends TestCase
{
    public function testInvalidResult()
    {
        $result = new Result();
        $this->assertFalse($result->isValid());
        $this->assertNull($result->getSession());
    }

    public function testOneParam()
    {
        $session = new PersistentSession();
        $result = new Result($session);
        $this->assertFalse($result->isValid());
        $this->assertSame($session, $result->getSession());
    }

    public function testValidResult()
    {
        $session = new PersistentSession();
        $result = new Result($session, true);
        $this->assertTrue($result->isValid());
        $this->assertSame($session, $result->getSession());
    }
}
