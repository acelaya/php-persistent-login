<?php
namespace AcelayaTest\PersistentLogin;

use Acelaya\PersistentLogin\Adapter\StorageInterface;
use Acelaya\PersistentLogin\Adapter\TokenInterface;
use Acelaya\PersistentLogin\Model\PersistentSession;
use Acelaya\PersistentLogin\PersistentLoginService;
use AcelayaTest\PersistentLogin\Mock\AuthenticationAdapterMock;
use AcelayaTest\PersistentLogin\Mock\IdentityMock;
use AcelayaTest\PersistentLogin\Mock\StorageAdapterMock;
use AcelayaTest\PersistentLogin\Mock\TokenAdapterMock;
use PHPUnit_Framework_TestCase as TestCase;

class PersistentLoginServiceTest extends TestCase
{
    /**
     * @var PersistentLoginService
     */
    protected $persistentLogin;
    /**
     * @var array
     */
    protected $sessions;
    /**
     * @var TokenInterface
     */
    protected $tokenAdapter;
    /**
     * @var StorageInterface
     */
    protected $storageAdapter;

    public function setUp()
    {
        $this->sessions = [];
        $this->storageAdapter = new StorageAdapterMock($this->sessions);
        $this->tokenAdapter = new TokenAdapterMock();
        $this->persistentLogin = new PersistentLoginService($this->storageAdapter, $this->tokenAdapter);
    }

    public function testCreateNewLogin()
    {
        $this->assertEmpty($this->sessions);
        $this->persistentLogin->createNewLogin(new IdentityMock());
        $this->assertCount(1, $this->sessions);
    }

    public function testHasPersistentLogin()
    {
        $this->assertFalse($this->persistentLogin->hasPersistentLogin());
        $this->tokenAdapter->setToken('foo', (new \DateTime())->add(new \DateInterval('P1D')));
        $this->assertTrue($this->persistentLogin->hasPersistentLogin());
    }

    public function testInvalidateCurrentToken()
    {
        // Invalidate with no token
        $this->persistentLogin->invalidateCurrentLogin();

        // Invalidate with token
        $this->storageAdapter->persistSession((new PersistentSession())->setToken('foo_token'));
        $this->tokenAdapter->setToken('foo_token', (new \DateTime())->add(new \DateInterval('P1D')));
        $this->assertCount(1, $this->sessions);
        $this->assertTrue($this->tokenAdapter->hasToken());

        $this->persistentLogin->invalidateCurrentLogin();
        $this->assertEmpty($this->sessions);
        $this->assertFalse($this->tokenAdapter->hasToken());
    }

    public function testAuthenticateWithNoPersistentLogin()
    {
        $result = $this->persistentLogin->authenticate(new AuthenticationAdapterMock([new IdentityMock()]));
        $this->assertFalse($result->isValid());
    }

    public function testAuthenticateWithPersistentLogin()
    {
        $this->tokenAdapter->setToken('foo_token', (new \DateTime())->add(new \DateInterval('P1D')));
        $result = $this->persistentLogin->authenticate(new AuthenticationAdapterMock([new IdentityMock()]));
        $this->assertFalse($result->isValid());
    }

    public function testAuthenticateWithSession()
    {
        $expirationDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $validIdentity = new IdentityMock();
        $this->storageAdapter->persistSession(
            (new PersistentSession())->setToken('foo_token')
                                     ->setValid()
                                     ->setIdentity($validIdentity)
                                     ->setExpirationDate($expirationDate)
        );
        $this->tokenAdapter->setToken('foo_token', $expirationDate);
        $result = $this->persistentLogin->authenticate(new AuthenticationAdapterMock([$validIdentity]));
        $this->assertTrue($result->isValid());
    }

    public function testAuthenticationWithExpiredSession()
    {
        $expirationDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $this->storageAdapter->persistSession(
            (new PersistentSession())->setToken('foo_token')
        );
        $this->tokenAdapter->setToken('foo_token', $expirationDate);
        $result = $this->persistentLogin->authenticate(new AuthenticationAdapterMock([new IdentityMock()]));
        $this->assertFalse($result->isValid());
    }
}
