<?php
namespace Fp\BadaBoomBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use BadaBoom\ChainNode\AbstractChainNode;
use BadaBoom\Context;

use Fp\BadaBoomBundle\ChainNode\ChainNodeManager;
use Fp\BadaBoomBundle\ChainNode\ChainNodeManagerInterface;

class LogControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldLogCorrectException()
    {
        $client = static::createClient();

        /** @var $chainNodeManager ChainNodeManagerInterface */
        $chainNodeManager = new ChainNodeManager();
        $chainNodeManager->addSender('test', new TestSender);

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
            "exception 'Fp\BadaBoomBundle\JsLogger\JavascriptException' with message 'Test message' in fileName:10",
            file_get_contents(sys_get_temp_dir().'/temp.txt')
        );
    }
}

class TestSender extends AbstractChainNode
{
    /**
     * @param Context $context
     *
     * @return void
     */
    function handle(Context $context)
    {
        file_put_contents(sys_get_temp_dir().'/temp.txt', $context->getException());
    }

}
