<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use Symfony\Component\HttpFoundation\Request;

use Fp\BadaBoomBundle\ChainNode\Provider\RequestProvider;
use BadaBoom\Context;

class RequestProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @test
     */
    public function shouldBeSubClassOfAbstractSender()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Provider\RequestProvider');
        $this->assertTrue($rc->isSubclassOf('BadaBoom\ChainNode\AbstractChainNode'));
    }

    /**
     *
     * @test
     */
    public function shouldImplementEventSubscriberInterface()
    {
        $rc = new \ReflectionClass('Fp\BadaBoomBundle\ChainNode\Provider\RequestProvider');
        $this->assertTrue($rc->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new RequestProvider;
    }

    /**
     * @test
     */
    public function shouldListentForGetResponseEventAndGetRequestFromIt()
    {
        $expectedRequest = Request::createFromGlobals();

        $getResponseEventMock = $this->createGetResponseEvent();
        $getResponseEventMock
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($expectedRequest))
        ;

        $proviver = new RequestProvider;

        $proviver->onEarlyKernelRequest($getResponseEventMock);

        $this->assertAttributeSame($expectedRequest, 'request', $proviver);
    }

    public function shouldDoNothingIfRequestNotSet()
    {
        $contextMock = $this->createContextMock();
        $contextMock
            ->expects($this->never())
            ->method('setVar')
        ;

        $proviver = new RequestProvider;

        $proviver->handle($contextMock);
    }

    /**
     * @test
     */
    public function shouldDelegateHandlingToNextNode()
    {
        $context = new Context(new \Exception);

        $nextChainNodeMock = $this->createChainNodeMock();
        $nextChainNodeMock
            ->expects($this->once())
            ->method('handle')
            ->with($context)
        ;

        $proviver = new RequestProvider;

        $proviver->nextNode($nextChainNodeMock);

        $proviver->handle($context);
    }

    /**
     * @test
     */
    public function shouldAllowSetRequest()
    {
        $proviver = new RequestProvider;

        $proviver->setRequest(Request::createFromGlobals());
    }

    /**
     * @test
     */
    public function shouldFillServerSection()
    {
        $expectedServerData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = $expectedServerData
        );

        $context = new Context(new \Exception);

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle($context);
                     ;
        $this->assertTrue($context->hasVar('server'));
        $this->assertSame($expectedServerData, $context->getVar('server'));
    }

    /**
     * @test
     */
    public function shouldFillCookieSection()
    {
        $expectedCookieData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = array(),
            $attributes = array(),
            $cookies = $expectedCookieData,
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle($context);
        ;
        $this->assertTrue($context->hasVar('cookies'));
        $this->assertSame($expectedCookieData, $context->getVar('cookies'));
    }

    /**
     * @test
     */
    public function shouldFillGetSection()
    {
        $expectedQueryData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = $expectedQueryData,
            $request = array(),
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle($context);
        ;
        $this->assertTrue($context->hasVar('query'));
        $this->assertSame($expectedQueryData, $context->getVar('query'));
    }

    /**
     * @test
     */
    public function shouldFillRequestSection()
    {
        $expectedRequestData = array(
            'foo' => 'foo',
            'bar' => 'bar'
        );

        $request = new Request(
            $query = array(),
            $request = $expectedRequestData,
            $attributes = array(),
            $cookies = array(),
            $files = array(),
            $server = array()
        );

        $context = new Context(new \Exception);

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle($context);
        ;
        $this->assertTrue($context->hasVar('request'));
        $this->assertSame($expectedRequestData, $context->getVar('request'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Badaboom\Context
     */
    protected function createContextMock()
    {
        return $this->getMock('BadaBoom\Context', array(), array(new \Exception));
    }

    protected function createGetResponseEvent()
    {
        return $this->getMock('Symfony\Component\HttpKernel\Event\GetResponseEvent', array(), array(), '', false);
    }

    protected function createChainNodeMock()
    {
        return $this->getMock('BadaBoom\ChainNode\ChainNodeInterface');
    }
}