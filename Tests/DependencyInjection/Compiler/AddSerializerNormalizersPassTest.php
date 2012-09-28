<?php
namespace Fp\BadaBoomBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;

class AddSerializerNormalizersPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new AddSerializerNormalizersPass();
    }

    /**
     * @test
     */
    public function shouldFindFpBadaBoomNormalizerTags()
    {
        $containerBuilderMock = $this->createContainerBuilderMock();
        $containerBuilderMock
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(
                $this->equalTo('fp_badaboom.normalizer')
            )
            ->will($this->returnValue(array()))
        ;
        $containerBuilderMock
            ->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($this->createDefinitionMock()))
        ;

        $pass = new AddSerializerNormalizersPass();
        $pass->process($containerBuilderMock);
    }

    /**
     * @test
     */
    public function shouldReplaceFirstArgumentOfSerializerServiceWithTaggedNormalizers()
    {
        $tags = array(
            'a_normalizer_id' => array(),
            'an_other_normalizer_id' => array(),
        );

        $expectedNormalizers = array(
            new Reference('a_normalizer_id'),
            new Reference('an_other_normalizer_id'),
        );

        $serializerDefinitionMock = $this->createDefinitionMock();
        $serializerDefinitionMock
            ->expects($this->once())
            ->method('replaceArgument')
            ->with(
                $this->equalTo($firstArgument = 0),
                $this->equalTo($expectedNormalizers)
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

        $pass = new AddSerializerNormalizersPass();
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
