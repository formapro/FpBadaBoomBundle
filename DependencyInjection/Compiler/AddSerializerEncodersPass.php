<?php
namespace Fp\BadaBoomBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddSerializerEncodersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $encoders = array();
        foreach ($container->findTaggedServiceIds('fp_badaboom.encoder') as $id => $attributes) {
            $encoders[] = new Reference($id);
        }

        $container->getDefinition('fp_badaboom.serializer')->replaceArgument(1, $encoders);
    }
}
