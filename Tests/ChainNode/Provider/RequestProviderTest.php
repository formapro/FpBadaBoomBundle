<?php
namespace Fp\BadaBoomBundle\Tests\ChainNode\Sender;

use Symfony\Component\HttpFoundation\Request;

use Fp\BadaBoomBundle\ChainNode\Provider\RequestProvider;
use BadaBoom\DataHolder\DataHolder;

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
        $dataHodlerMock = $this->createDataHolderMock();
        $dataHodlerMock
            ->expects($this->never())
            ->method('set')
        ;

        $proviver = new RequestProvider;

        $proviver->handle(new \Exception(), $dataHodlerMock);
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

        $proviver = new RequestProvider;

        $proviver->nextNode($nextChainNodeMock);

        $proviver->handle($expectedException, $expectedDataHolderMock);
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

        $dataHolder = new DataHolder;

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle(new \Exception(), $dataHolder);
                     ;
        $this->assertTrue($dataHolder->has('server'));
        $this->assertSame($expectedServerData, $dataHolder->get('server'));
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

        $dataHolder = new DataHolder;

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle(new \Exception(), $dataHolder);
        ;
        $this->assertTrue($dataHolder->has('cookies'));
        $this->assertSame($expectedCookieData, $dataHolder->get('cookies'));
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

        $dataHolder = new DataHolder;

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle(new \Exception(), $dataHolder);
        ;
        $this->assertTrue($dataHolder->has('query'));
        $this->assertSame($expectedQueryData, $dataHolder->get('query'));
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

        $dataHolder = new DataHolder;

        $proviver = new RequestProvider;
        $proviver->setRequest($request);

        $proviver->handle(new \Exception(), $dataHolder);
        ;
        $this->assertTrue($dataHolder->has('request'));
        $this->assertSame($expectedRequestData, $dataHolder->get('request'));
    }

    protected function createDataHolderMock()
    {
        return $this->getMock('BadaBoom\DataHolder\DataHolderInterface');
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