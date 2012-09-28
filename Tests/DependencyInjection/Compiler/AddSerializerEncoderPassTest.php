<?php
namespace Fp\BadaBoomBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;

class AddSerializerEncoderPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new AddSerializerEncodersPass();
    }

    /**
     * @test
     */
    public function shouldFindFpBadaBoomEncoderTags()
    {
        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(
                $this->equalTo('fp_badaboom.encoder')
            )
            ->will($this->returnValue(array()))
        ;
        $containerBuilderMock
            ->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($this->createDefinitionMock()))
        ;

        $pass = new AddSerializerEncodersPass();
        $pass->process($containerBuilderMock);
    }

    /**
     * @test
     */
    public function shouldReplaceSecondArgumentOfSerializerServiceWithTaggedEncoders()
    {
        $tags = array(
            'an_encoder_id' => array(),
            'another_encoder_id' => array(),
        );

        $expectedEncoders = array(
            new Reference('an_encoder_id'),
            new Reference('another_encoder_id'),
        );

        $serializerDefinitionMock = $this->createDefinitionMock();
        $serializerDefinitionMock
            ->expects($this->once())
            ->method('replaceArgument')
            ->with(
                $this->equalTo($secondArgument = 1),
                $this->equalTo($expectedEncoders)
            )
        ;

        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->will($this->returnValue($tags))
        ;
        $containerBuilderMock
            ->expects($this->once())
            ->method('getDefinition')
            ->will($this->returnValue($serializerDefinitionMock))
        ;

        $pass = new AddSerializerEncodersPass();
        $pass->process($containerBuilderMock);
    }

    protected function createContainerBuilderMock()
    {
        return $this->getMock(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            array('findTaggedServiceIds', 'getDefinition'),
            array(),
            '',
            false
        );
    }

    protected function createDefinitionMock()
    {
        return $this->getMock(
            'Symfony\Component\DependencyInjection\Definition',
            array('replaceArgument'),
            array(),
            '',
            false
        );
    }
}
