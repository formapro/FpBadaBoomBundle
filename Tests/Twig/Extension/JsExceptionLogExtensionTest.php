<?php
namespace Fp\BadaBoomBundle\Tests\Twig\Extension;

use Fp\BadaBoomBundle\Twig\Extension\JsExceptionLogExtension;

class JsExceptionLogExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithRouter()
    {
        new JsExceptionLogExtension($this->createRouterMock());
    }

    /**
     * @test
     */
    public function shouldDetermineAllRequiredTwigFunction()
    {
        $twigExtension = new JsExceptionLogExtension($this->createRouterMock());
        $methods = $twigExtension->getFunctions();

        $this->assertArrayHasKey('fp_badaboom_js_error_logger', $methods);
        $this->assertAttributeEquals(
            'initErrorLogger',
            'method',
            $methods['fp_badaboom_js_error_logger']
        );
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

        $twigExtension = new JsExceptionLogExtension($router);

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
