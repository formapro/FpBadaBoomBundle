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

        $crawler = $client->request('GET', '/fp-badaboom/log', array(
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

        $this->assertStringStartsWith(
            "exception 'Fp\BadaBoomBundle\Exception\JavascriptException' with message 'Test message' in fileName:10",
            $sender->message
        );
    }
}

class TestSender extends AbstractChainNode
{
    public $message;
    /**
     * @param Context $context
     *
     * @return void
     */
    function handle(Context $context)
    {
        $this->message = (string) $context->getException();
    }

}
