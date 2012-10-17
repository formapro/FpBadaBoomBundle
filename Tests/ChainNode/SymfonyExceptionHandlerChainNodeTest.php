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
    protected $fumocker;
    
    public function setUp()
    {
        $this->fumocker = new \Fumocker\Fumocker();

        $mock = $this->fumocker->getMock('Fp\BadaBoomBundle\ChainNode', 'php_sapi_name');
        $mock
            ->expects($this->any())
            ->method('php_sapi_name')
            ->will($this->returnValue('not-a-cli'))
        ;
        
        ob_start();
    }
    
    public function tearDown()
    {
        $this->fumocker->cleanup();
        
        ob_clean();
    }
    
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
        new SymfonyExceptionHandlerChainNode($debug = true);
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

        $sender = new SymfonyExceptionHandlerChainNode($debug = true);

        $sender->nextNode($nextChainNodeMock);

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