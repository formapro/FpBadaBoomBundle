<?php
namespace Fp\BadaBoomBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerNormalizersPass;
use Fp\BadaBoomBundle\DependencyInjection\Compiler\AddSerializerEncodersPass;

class FpBadaBoomBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddSerializerNormalizersPass);
        $container->addCompilerPass(new AddSerializerEncodersPass);
    }
}
