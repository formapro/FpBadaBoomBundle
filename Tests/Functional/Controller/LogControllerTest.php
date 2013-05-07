<?php
namespace Fp\BadaBoomBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

use Fp\BadaBoomBundle\ChainNode\ChainNodeManager;

class LogControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldLogCorrectException()
    {
        $client = static::createClient();

        $chainNodeManager = new ChainNodeManager();
        $sender = new TestSender;
        $chainNodeManager->addSender('test', $sender);

        $logger = $client->getContainer()->get('fp_badaboom.logger');
        $logger->registerChain($chainNodeManager->get('test'));

        $crawler = $client->request('GET', '/fp-badaboom/js-log', array(
            'msg' => 'Test message',
            'level' => 'error',
            'context' => array(
                'file' => 'fileName',
                'line' => 10,
                'browser' => 'browser',
                'page' => 'page',
            ),
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'image/gif'
            )
        );

        $exception = $sender->context->getException();

        $this->assertInstanceOf('Fp\BadaBoomBundle\Exception\JavascriptException', $exception);

        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals('fileName', $exception->getFile());
        $this->assertEquals(10, $exception->getLine());
    }

    /**
     * @test
     */
    public function shouldReturnBadRequestPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fp-badaboom/js-log', array(
            'msg' => 'Test message',
            'level' => 'error',
        ));

        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode()
        );
    }
}

class TestSender extends AbstractChainNode
{
    /**
     * @var Context
     */
    public $context;
    /**
     * @param Context $context
     *
     * @return void
     */
    function handle(Context $context)
    {
        $this->context = $context;
    }

}
