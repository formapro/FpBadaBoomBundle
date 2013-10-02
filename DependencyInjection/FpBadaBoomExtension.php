<?php

namespace Fp\BadaBoomBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class FpBadaBoomExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('chain_nodes.xml');
        
        // load it at the end. but before any changes to container builder.
        if (class_exists('UniversalErrorCatcher_Catcher')) {
            $loader->load('universal_error_catcher.xml');
        }

        if (version_compare(Kernel::VERSION, '2.3.0', '<')) {
            $container->removeDefinition('fp_badaboom.console_exception_listener');
        }
    }
}