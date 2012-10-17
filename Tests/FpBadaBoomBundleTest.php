<?php
namespace Fp\BadaBoomBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Fp\BadaBoomBundle\FpBadaBoomBundle;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddChainNodesToManagerPass;

class FpBadaBoomBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new FpBadaBoomBundle;
    }

    /**
     * @test
     */
    public function shouldAddSerializerNormalizersPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;

        $container = new ContainerBuilder();

        $bundle->build($container);

        $this->assertContainerContainsCompilerPass($container, new AddSerializerNormalizersPass);
    }

    /**
     * @test
     */
    public function shouldAddSerializerEncodersPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;

        $container = new ContainerBuilder();

        $bundle->build($container);

        $this->assertContainerContainsCompilerPass($container, new AddSerializerEncodersPass);
    }

    /**
     * @test
     */
    public function shouldAddAddChainNodesToManagerPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;

        $container = new ContainerBuilder();

        $bundle->build($container);

        $this->assertContainerContainsCompilerPass($container, new AddChainNodesToManagerPass);
    }
    
    protected function assertContainerContainsCompilerPass($container, $pass)
    {
        $this->assertContains(
            $pass,
            $container->getCompilerPassConfig()->getPasses(),
            $message = '',
            $ignoreCase = false,
            $checkForObjectIdentity = false
        );
    }
}