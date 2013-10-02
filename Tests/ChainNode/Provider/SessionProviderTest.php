<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use Fp\BadaBoomBundle\ChainNode\Provider\SessionProvider;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SessionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Provider\SessionProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithSecurityContextAsArgument()
    {
        new SessionProvider($this->createSessionMock());
    }

    /**
     * @test
     */
    public function shouldAddSessionDataToDefaultSectionIfStarted()
    {
        $expectedSessionData = array(
            'foo' => 'foo',
            'bar' => new \stdClass(),
            'olo' => 123
        );

        $sessionMock = $this->createSessionMock();
        $sessionMock
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($expectedSessionData))
        ;
        $sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true))
        ;

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo('session'),
                $this->equalTo($expectedSessionData)
            )
        ;

        $sessionProvider = new SessionProvider($sessionMock);

        $sessionProvider->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldAddSessionDataToCustomSection()
    {
        $expectedCustomSectionName = 'custom_section_name';

        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->once())
            ->method('setVar')
            ->with(
                $this->equalTo($expectedCustomSectionName)
            )
        ;

        $sessionMock = $this->createSessionMock();
        $sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true))
        ;

        $sessionProvider = new SessionProvider($sessionMock, $expectedCustomSectionName);

        $sessionProvider->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldDoNothingAndDelegateHandlingToNextNodeIfSessionNotSet()
    {
        $context = $this->createContextMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sessionProvider = new SessionProvider;

        $sessionProvider->nextNode($nextChainNodeMock);

        $sessionProvider->handle($context);
    }

    /**
     * @test
     */
    public function shouldDoNothingAndDelegateHandlingToNextNodeIfSessionNotStarted()
    {
        $context = $this->createContextMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;


        $sessionMock = $this->createSessionMock();
        $sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false))
        ;

        $sessionProvider = new SessionProvider($sessionMock);

        $sessionProvider->nextNode($nextChainNodeMock);

        $sessionProvider->handle($context);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = $this->createContextMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sessionProvider = new SessionProvider($this->createSessionMock());

        $sessionProvider->nextNode($nextChainNodeMock);

        $sessionProvider->handle($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function createSessionMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Badaboom\Context
     */
    protected function createContextMock()
    {
        return $this->getMock('BadaBoom\Context', array(), array(new \Exception));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BadaBoom\ChainNode\ChainNodeInterface
     */
    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }
}