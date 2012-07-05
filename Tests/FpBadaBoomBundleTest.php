<?php
namespace Fp\BadaBoomBundle\Tests;

use Fp\BadaBoomBundle\FpBadaBoomBundle;

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

        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with(
                $this->isInstanceOf('Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass')
            )
        ;

        $bundle->build($containerBuilderMock);
    }

    /**
     * @test
     */
    public function shouldAddSerializerEncodersPassWhileBuilding()
    {
        $bundle = new FpBadaBoomBundle;

        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with(
                $this->isInstanceOf('Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass')
            )
        ;

        $bundle->build($containerBuilderMock);
    }

    protected function createContainerBuilderMock()
    {
        return $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array(), array(), '', false);
    }
}