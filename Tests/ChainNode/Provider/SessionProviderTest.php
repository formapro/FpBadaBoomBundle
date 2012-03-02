<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use Fp\BadaBoomBundle\ChainNode\Provider\SessionProvider;

class SecurityContextProviderTest extends \PHPUnit_Framework_TestCase
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
    public function shouldAddSessionDataToDefaultSection()
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

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('session'),
                $this->equalTo($expectedSessionData)
            )
        ;

        $sessionProvider = new SessionProvider($sessionMock);

        $sessionProvider->handle(new \Exception(), $dataHolderMock);
    }

    /**
     * @test
     */
    public function shouldAddSessionDataToCustomSection()
    {
        $expectedCustomSectionName = 'custom_section_name';

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($expectedCustomSectionName)
            )
        ;

        $sessionProvider = new SessionProvider(
            $this->createSessionMock(),
            $expectedCustomSectionName
        );

        $sessionProvider->handle(new \Exception(), $dataHolderMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $expectedException = new \Exception();
        $expectedDataHolder = $this->createDataHolderMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedException),
                $this->equalTo($expectedDataHolder)
            )
        ;

        $sessionProvider = new SessionProvider($this->createSessionMock());

        $sessionProvider->nextNode($nextChainNodeMock);

        $sessionProvider->handle($expectedException, $expectedDataHolder);
    }

    protected function createSessionMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
    }

    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }
}