<?php
namespace Fp\BadaBoomBundle\Tests\JsLogger;

use Fp\BadaBoomBundle\JsLogger\TwigExtension;

class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldDetermineAllRequiredTwigFunction()
    {
        $twigExtension = new TwigExtension($this->createRouterMock());

        $this->assertArrayHasKey('fp_badaboom_js_error_logger', $twigExtension->getFunctions());
    }

    /**
     * @test
     */
    public function shouldGenerateProperScript()
    {
        $router = $this->createRouterMock();
        $router
            ->expects($this->once())
            ->method('generate')
            ->with('fp_badaboom_js_logger_log')
        ;

        $twigExtension = new TwigExtension($router);

        $result = $twigExtension->initErrorLogger();

        $this->assertNotEmpty($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected function createRouterMock()
    {
        return $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
    }
}
