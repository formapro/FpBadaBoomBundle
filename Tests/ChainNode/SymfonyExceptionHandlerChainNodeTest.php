<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use Fp\BadaBoomBundle\ChainNode\SymfonyExceptionHandlerChainNode;
use BadaBoom\Context;

/**
 * @author Kotlyar Maksim <kotlyar.maksim@gmail.com>
 * @since 4/10/12
 */
class SymfonyExceptionHandlerChainNodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\SymfonyExceptionHandlerChainNode');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithExceptionHandlerAsArgument()
    {
        new SymfonyExceptionHandlerChainNode($this->createExceptionHandlerMock());
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = new Context(new \Exception());

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $sender = new SymfonyExceptionHandlerChainNode($this->createExceptionHandlerMock());

        $sender->nextNode($nextChainNodeMock);

        $sender->handle($context);
    }

    /**
     * @test
     */
    public function shouldProxyExceptionToExceptionHandler()
    {
        $context = new Context($expectedException = new \Exception());

        $exceptionHandlerMock = $this->createExceptionHandlerMock();
        $exceptionHandlerMock
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedException)
            )
        ;

        $sender = new SymfonyExceptionHandlerChainNode($exceptionHandlerMock);

        $sender->handle($context);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\BadaBoom\ChainNode\ChainNodeInterface
     */
    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpKernel\Debug\ExceptionHandler
     */
    protected function createExceptionHandlerMock()
    {
        return $this->getMock('Symfony\Component\HttpKernel\Debug\ExceptionHandler', array('handle'));
    }
}