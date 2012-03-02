<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Provider;

use Fp\BadaBoomBundle\ChainNode\Provider\SecurityContextProvider;

class SecurityContextProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubclassOfAbstractProvider()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Provider\SecurityContextProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\Provider\AbstractProvider'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithSecurityContextAsArgument()
    {
        new SecurityContextProvider($this->createSecurityContextMock());
    }

    /**
     * @test
     */
    public function shouldDoNothingIfContextNotHaveToken()
    {
        $chain = new SecurityContextProvider($this->createSecurityContextMock());

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->never())
            ->method('set')
        ;

        $chain->handle(new \Exception(), $dataHolderMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextChainNode()
    {
        $expectedException = new \Exception();
        $expectedDataHolder = $this->createDataHolderMock();

        $chain = new SecurityContextProvider($this->createSecurityContextMock());

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedException),
                $this->equalTo($expectedDataHolder)
            )
        ;

        $chain->nextNode($nextChainNodeMock);

        $chain->handle($expectedException, $expectedDataHolder);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationIfContextHaveTokenWithUserAsString()
    {
        $expectedDefaultSection = 'security';
        $expectedUser = 'the user';
        $expectedUserData = array(
            'user' => $expectedUser
        );

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createSecurityContextMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($expectedDefaultSection),
                $this->equalTo($expectedUserData)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock);

        $chain->handle(new \Exception(), $dataHolderMock);
    }

    /**
    * @test
    */
    public function shouldAddUserInformationToCustomSectionIfContextHaveTokenWithUserAsString()
    {
        $expectedCustomSection = 'custom_security_section';
        $expectedUser = 'the user';

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createSecurityContextMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($expectedCustomSection)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock, $expectedCustomSection);

        $chain->handle(new \Exception(), $dataHolderMock);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationIfContextHaveTokenWithUserInterface()
    {
        $expectedDefaultSection = 'security';

        $expectedUsername = 'the user object username';
        $expectedUser = $this->createUserMock();
        $expectedUser
            ->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue($expectedUsername))
        ;

        $expectedUserData = array(
            'user' => $expectedUsername
        );

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createSecurityContextMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($expectedDefaultSection),
                $this->equalTo($expectedUserData)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock);

        $chain->handle(new \Exception(), $dataHolderMock);
    }

    /**
     * @test
     */
    public function shouldAddUserInformationToCustomSectionIfContextHaveTokenWithUserInterface()
    {
        $expectedCustomSection = 'custom_security_section';
        $expectedUser = $this->createUserMock();

        $tokenMock = $this->createTokenMock();
        $tokenMock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($expectedUser))
        ;

        $securityContextMock = $this->createSecurityContextMock();
        $securityContextMock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($tokenMock))
        ;

        $dataHolderMock = $this->createDataHolderMock();
        $dataHolderMock
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo($expectedCustomSection)
            )
        ;

        $chain = new SecurityContextProvider($securityContextMock, $expectedCustomSection);

        $chain->handle(new \Exception(), $dataHolderMock);
    }

    protected function createSecurityContextMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    protected function createTokenMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }

    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    protected function createUserMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
    }
}