<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use Fp\BadaBoomBundle\ChainNode\Sender\HtmlSender;

class HtmlSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Sender\HtmlSender');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithExceptionHandlerAsArgument()
    {
        new HtmlSender($this->createExceptionHandlerMock());
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $expectedException = new \Exception();
        $expectedDataHolderMock = $this->createDataHolderMock();

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with(
            $this->equalTo($expectedException),
            $this->equalTo($expectedDataHolderMock)
        )
        ;

        $sender = new HtmlSender($this->createExceptionHandlerMock());

        $sender->nextNode($nextChainNodeMock);

        $sender->handle($expectedException, $expectedDataHolderMock);
    }

    /**
     * @test
     */
    public function shouldProxyExceptionToExceptionHandler()
    {
        $expectedException = new \Exception();

        $exceptionHandlerMock = $this->createExceptionHandlerMock();
        $exceptionHandlerMock
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo($expectedException)
            )
        ;

        $sender = new HtmlSender($exceptionHandlerMock);

        $sender->handle($expectedException, $this->createDataHolderMock());
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
    }

    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }

    protected function createExceptionHandlerMock()
    {
        return $this->getMock('Symfony\Component\HttpKernel\Debug\ExceptionHandler', array('handle'));
    }
}