<?php
namespace Fp\BadaBoomBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddChainNodesToManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $managerDefinition = $container->getDefinition('fp_badaboom.chain_node_manager');
        
        foreach ($container->findTaggedServiceIds('fp_badaboom.filter_chain_node') as $id => $attributes) {
            $chain = false == empty($attributes['chain']) ? $attributes['chain'] : 'default';
            
            $managerDefinition->addMethodCall('addFilter', array(
                $chain,
                new Reference($id)
            ));
        }
        
        foreach ($container->findTaggedServiceIds('fp_badaboom.provider_chain_node') as $id => $attributes) {
            $chain = false == empty($attributes['chain']) ? $attributes['chain'] : 'default';

            $managerDefinition->addMethodCall('addProvider', array(
                $chain,
                new Reference($id)
            ));
        }

        foreach ($container->findTaggedServiceIds('fp_badaboom.sender_chain_node') as $id => $attributes) {
            $chain = false == empty($attributes['chain']) ? $attributes['chain'] : 'default';

            $managerDefinition->addMethodCall('addSender', array(
                $chain,
                new Reference($id)
            ));
        }
    }
}