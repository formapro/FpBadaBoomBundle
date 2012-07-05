<?php
namespace Fp\BadaBoomBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddSerializerNormalizersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $normalizers = array();
        foreach ($container->findTaggedServiceIds('fp.badaboom.normalizer') as $id => $attributes) {
            $normalizers[] = new Reference($id);
        }

        $container->getDefinition('fp.badaboom.serializer')->replaceArgument(0, $normalizers);
    }
}
